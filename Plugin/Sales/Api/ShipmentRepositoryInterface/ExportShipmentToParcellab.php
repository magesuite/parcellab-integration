<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Plugin\Sales\Api\ShipmentRepositoryInterface;

class ExportShipmentToParcellab
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface
     */
    protected $parcellabExportManagement;

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface $parcellabExportManagement,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configuration = $configuration;
        $this->parcellabExportManagement = $parcellabExportManagement;
        $this->logger = $logger;
    }

    public function beforeSave(
        \Magento\Sales\Api\ShipmentRepositoryInterface $subject,
        \Magento\Sales\Api\Data\ShipmentInterface $entity
    ) {
        if (!$this->configuration->isEnabled()) {
            return null;
        }
        $this->parcellabExportManagement->exportShipment($entity);

        return null;
    }
}
