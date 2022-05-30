<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\InventorySalesApi\Test\OriginalSequenceBuilder $sequenceBuilder */
$sequenceBuilder = $objectManager->get(\Magento\InventorySalesApi\Test\OriginalSequenceBuilder::class);
/** @var \Magento\SalesSequence\Model\EntityPool $entityPool */
$entityPool = $objectManager->get(\Magento\SalesSequence\Model\EntityPool::class);
/** @var \Magento\SalesSequence\Model\Config $sequenceConfig */
$sequenceConfig = $objectManager->get(\Magento\SalesSequence\Model\Config::class);
/** @var Magento\Framework\App\RequestInterface $request */
$request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
/** @var \Magento\Store\Api\Data\StoreInterface $store */
$store = $objectManager->create(\Magento\Store\Model\Store::class);

$objectManager->addSharedInstance($sequenceBuilder, \Magento\SalesSequence\Model\Builder::class);
$store->load('default');
$rootCategoryId = $store->getRootCategoryId();

$data = [
    'website_code' => 'be_website',
    'website_name' => 'BE Website',
    'store_code' => 'be_nl',
    'store_name' => 'BE NL Store',
    'store_view_code' => 'be_store_view',
    'store_view_name' => 'BE Store View'
];

$params = [
    'code' => $data['website_code'],
    'name' => $data['website_name'],
    'is_default' => '0'
];

/** @var \Magento\Store\Model\Website $website */
$website = $objectManager->create(\Magento\Store\Model\Website::class);
$website->setData($params);
$request->setParams(["website" => $params]); //fix for cleverreach module
$website->save();

/** @var \Magento\Store\Model\Store $store */
$store = $objectManager->create(\Magento\Store\Model\Store::class);
$store->setCode(
    $data['store_code']
)->setWebsiteId(
    $website->getId()
)->setName(
    $data['store_name']
)->setSortOrder(
    10
)->setIsActive(
    1
);

/** @var \Magento\Store\Api\Data\GroupInterface $group */
$group = $objectManager->create(\Magento\Store\Api\Data\GroupInterface::class);
$group->setName($data['store_view_name']);
$group->setCode($data['store_view_code']);
$group->setWebsiteId($website->getId());
$group->setDefaultStoreId($store->getId());
$group->setRootCategoryId($rootCategoryId);
$group->save();

$website->setDefaultGroupId($group->getId());
$website->save();
$store->setGroupId($group->getId());
$store->save();

/**
 * Revert set original sequence builder to test sequence builder.
 */
$sequenceBuilder = $objectManager->get(\Magento\TestFramework\Db\Sequence\Builder::class);
$objectManager->addSharedInstance($sequenceBuilder, \Magento\SalesSequence\Model\Builder::class);

$objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->reinitStores();
