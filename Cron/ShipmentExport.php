<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Cron;

class ShipmentExport
{
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
        \CreativeStyle\ParcellabIntegration\Api\ParcellabExportManagementInterface $parcellabExportManagement
    ) {
        $this->configuration = $configuration;
        $this->parcellabExportManagement = $parcellabExportManagement;
    }

    public function execute()
    {
        if (!$this->configuration->isAutoExportEnabled()) {
            return false;
        }
        $this->parcellabExportManagement->bulkExport();

        return true;
    }
}
