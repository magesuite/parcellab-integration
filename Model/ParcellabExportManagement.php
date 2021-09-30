<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Model;

class ParcellabExportManagement implements \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface
{
    public const TRACKING_EXPORTED = 1;

    /**
     * @var RequestPreprocessors\OrderCreation
     */
    protected $orderCreationPreprocessor;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Api\ServiceAdapterInterface
     */
    protected $serviceAdapter;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Sales\Api\ShipmentTrackRepositoryInterface
     */
    protected $shipmentTrackRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \CreativeStyle\ParcellabIntegration\Api\ServiceAdapterInterface $serviceAdapter,
        \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation $orderCreationPreprocessor,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderCreationPreprocessor = $orderCreationPreprocessor;
        $this->serviceAdapter = $serviceAdapter;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->configuration = $configuration;
        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->messageManager = $messageManager;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function exportShipment($shipment)
    {
        $trackings = $shipment->getTracks();

        if (!$this->configuration->isEnabled($shipment->getStoreId())) {
            return;
        }

        if (!$trackings) {
            try {
                $this->serviceAdapter->performRequestToApi(
                    $this->orderCreationPreprocessor,
                    [
                        \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::SHIPMENT_ARG => $shipment
                    ]
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $shipment->addComment(__("Shipment hasn't been exported to Parcellab. Error message: %1", $e->getMessage()));
                $this->logger->error(__("Shipment hasn't been exported to Parcellab. Error message: %1", $e->getMessage()));
            }
            return;
        }

        foreach ($trackings as $tracking) {
            if ($tracking->getExportedToParcellab()) {
                continue;
            }
            try {
                $isExported = $this->serviceAdapter->performRequestToApi(
                    $this->orderCreationPreprocessor,
                    [
                        \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::SHIPMENT_ARG => $shipment,
                        \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::TRACKING_ARG => $tracking->getData()
                    ]
                );
                $this->setExportedAndAddMessage($tracking, $isExported);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $tracking->getShipment()->addComment(__("Tracking #%1 hasn't been exported to Parcellab. Error message: %2", $tracking->getTitle(), $e->getMessage()));
                $this->logger->error(__("Tracking #%1 hasn't been exported to Parcellab. Error message: %2", $tracking->getTitle(), $e->getMessage()));
            }
        }
    }

    public function exportShipmentsByOrderId(int $orderId)
    {
        try {
            $this->searchCriteriaBuilder->addFilter('order_id', $orderId);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $shipments = $this->shipmentRepository->getList($searchCriteria);

            foreach ($shipments as $shipment) {
                $this->shipmentRepository->save($shipment);
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error(__('Something goes wrong with request: %1', $e->getMessage()));
        }
    }

    public function exportTracking(\Magento\Sales\Api\Data\ShipmentTrackInterface $tracking)
    {
        if ($tracking->getExportedToParcellab()) {
            return;
        }
        $trackingData = $tracking->getData();
        $shipment = $this->shipmentRepository->get($tracking->getParentId());

        try {
            $isExported = $this->serviceAdapter->performRequestToApi(
                $this->orderCreationPreprocessor,
                [
                    \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::SHIPMENT_ARG => $shipment,
                    \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::TRACKING_ARG => $trackingData
                ]
            );
            $this->setExportedAndAddMessage($tracking, $isExported);
            $this->shipmentRepository->save($tracking->getShipment());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $tracking->getShipment()->addComment(
                __("Tracking #%1 hasn't been exported to Parcellab. Error message: %2", $tracking->getTitle(), $e->getMessage())
            );
            $this->logger->error(__("Tracking #%1 hasn't been exported to Parcellab. Error message: %2", $tracking->getTrackNumber(), $e->getMessage()));
        }
    }

    public function bulkExport()
    {
        $this->searchCriteriaBuilder->addFilter('exported_to_parcellab', 0);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $trackings = $this->shipmentTrackRepository->getList($searchCriteria);
        $allowedStatuses = $this->configuration->getAllowedStatuses();

        foreach ($trackings as $tracking) {
            $order = $this->orderRepository->get((int)$tracking->getOrderId());

            if ($allowedStatuses && !in_array($order->getStatus(), $allowedStatuses)) {
                continue;
            }
            $this->exportTracking($tracking);
        }
    }

    protected function setExportedAndAddMessage(
        \Magento\Sales\Api\Data\ShipmentTrackInterface $tracking,
        $isExported
    ) {
        if ($isExported) {
            $tracking->setExportedToParcellab(self::TRACKING_EXPORTED);
            $tracking->getShipment()->addComment(
                __("Tracking number %1 has been exported to Parcellab", $tracking->getTrackNumber())
            );
        } else {
            $tracking->getShipment()->addComment(
                __('Tracking number %1 has not been exported to Parcellab', $tracking->getTrackNumber())
            );
        }
    }
}
