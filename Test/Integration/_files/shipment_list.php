<?php
\Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->get(\Magento\Sales\Api\Data\OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

$shipments = [
    [
        'increment_id' => '100000001',
        'shipping_address_id' => 1,
        'shipment_status' => \Magento\Sales\Model\Order\Shipment::STATUS_NEW,
        'store_id' => 1,
        'shipping_label' => 'shipping_label_100000001',
    ],
    [
        'increment_id' => '100000002',
        'shipping_address_id' => 3,
        'shipment_status' => \Magento\Sales\Model\Order\Shipment::STATUS_NEW,
        'store_id' => 1,
        'shipping_label' => 'shipping_label_100000002',
    ],
    [
        'increment_id' => '100000003',
        'shipping_address_id' => 3,
        'shipment_status' => \Magento\Sales\Model\Order\Shipment::STATUS_NEW,
        'store_id' => 1,
        'shipping_label' => 'shipping_label_100000003',
    ],
    [
        'increment_id' => '100000004',
        'shipping_address_id' => 4,
        'shipment_status' => 'closed',
        'store_id' => 1,
        'shipping_label' => 'shipping_label_100000004',
    ],
];

/** @var array $shipmentData */
foreach ($shipments as $shipmentData) {
    $items = [];
    foreach ($order->getItems() as $orderItem) {
        $items[$orderItem->getId()] = $orderItem->getQtyOrdered();
    }
    /** @var \Magento\Sales\Model\Order\Shipment $shipment */
    $shipment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Sales\Model\Order\ShipmentFactory::class)->create($order, $items);
    $shipment->setIncrementId($shipmentData['increment_id']);
    $shipment->setShippingAddressId($shipmentData['shipping_address_id']);
    $shipment->setShipmentStatus($shipmentData['shipment_status']);
    $shipment->setStoreId($shipmentData['store_id']);
    $shipment->setShippingLabel($shipmentData['shipping_label']);
    $shipment->save();
}
