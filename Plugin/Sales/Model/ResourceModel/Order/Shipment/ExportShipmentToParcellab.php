<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Plugin\Sales\Model\ResourceModel\Order\Shipment;

class ExportShipmentToParcellab
{
    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface
     */
    protected $parcellabExportManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface $parcellabExportManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configuration = $configuration;
        $this->parcellabExportManagement = $parcellabExportManagement;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    public function afterSave(
        \Magento\Sales\Model\ResourceModel\Order\Shipment $subject,
        \Magento\Sales\Model\ResourceModel\Order\Shipment $result,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if (!$this->configuration->isEnabled()) {
            return null;
        }

        $trackings = $this->request->getParam('tracking');

        try {
            $this->parcellabExportManagement->exportShipment($object);
            $this->messageManager->addSuccessMessage(__('The shipment has been exported to Parcellab.'));
            if ($trackings) {
                $this->messageManager->addSuccessMessage(__('%1 tracking numbers have been exported to Parcellab.', count($trackings)));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while exporting tracking.'));
        }

        return $result;
    }
}
