<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Plugin\Sales\Api\ShipmentTrackRepositoryInterface;

class ExportTrackingToParcellab
{
    /**
     * @var \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface
     */
    protected $parcellabExportManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface $parcellabExportManagement,
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->parcellabExportManagement = $parcellabExportManagement;
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    public function beforeSave(
        \Magento\Sales\Api\ShipmentTrackRepositoryInterface $subject,
        \Magento\Sales\Api\Data\ShipmentTrackInterface $entity
    ) {
        if (!$this->configuration->isEnabled()) {
            return null;
        }

        try {
            $this->parcellabExportManagement->exportTracking($entity);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            return null;
        }

        return null;
    }
}
