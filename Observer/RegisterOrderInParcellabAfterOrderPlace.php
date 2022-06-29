<?php

namespace CreativeStyle\ParcellabIntegration\Observer;

class RegisterOrderInParcellabAfterOrderPlace implements \Magento\Framework\Event\ObserverInterface
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

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface $parcellabExportManagement,
        \Psr\Log\LoggerInterface $logger
    ) {

        $this->configuration = $configuration;
        $this->parcellabExportManagement = $parcellabExportManagement;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();

        if (!$order instanceof \Magento\Sales\Api\Data\OrderInterface) {
            return;
        }

        if (!$this->configuration->isOrderRegisterEnabled()) {
            return;
        }

        try {
            $this->parcellabExportManagement->exportOrder($order);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }

    }
}
