<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get(\Magento\Framework\Registry::class);
/** @var \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory */
$orderFactory = $objectManager->get(\Magento\Sales\Api\Data\OrderInterfaceFactory::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Sales\Model\Order $order */
$order = $orderFactory->create()->loadByIncrementId('100000001');
$order->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
