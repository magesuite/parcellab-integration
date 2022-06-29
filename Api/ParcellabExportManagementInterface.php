<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Api;

interface ParcellabExportManagementInterface
{
    public function exportShipment($shipment);
    public function exportOrder($order);
    public function exportTracking(\Magento\Sales\Api\Data\ShipmentTrackInterface $tracking);
    public function bulkExport();
}
