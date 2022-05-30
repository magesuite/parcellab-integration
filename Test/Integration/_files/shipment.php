<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Store\Api\StoreRepositoryInterface $storeRepository */
$storeRepository = $objectManager->get(\Magento\Store\Api\StoreRepositoryInterface::class);

/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->get(\Magento\Sales\Api\Data\OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

$beStore = $storeRepository->get('be_nl');

$shipmentData = [
    'increment_id' => '100000001',
    'shipping_address_id' => 1,
    'shipment_status' => \Magento\Sales\Model\Order\Shipment::STATUS_NEW,
    'store_id' => $beStore->getId(),
    'shipping_label' => 'shipping_label_100000001',
];

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
