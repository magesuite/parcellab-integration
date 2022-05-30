<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\Config\Storage\WriterInterface $configWriter */
$configWriter = $objectManager->get(\Magento\Framework\App\Config\Storage\WriterInterface::class);
/** @var \Magento\Store\Api\StoreRepositoryInterface $storeRepository */
$storeRepository = $objectManager->get(\Magento\Store\Api\StoreRepositoryInterface::class);

$beStore = $storeRepository->get('be_nl');

$countryCodesConfiguration = json_encode([['store' => $beStore->getId(), 'client_country_code' => 'BEL']]);
$languageCodesConfiguration = json_encode([['store' => $beStore->getId(), 'client_language_code' => 'nl']]);

$configWriter->save('parcellab/payload_configuration/client_country_code', $countryCodesConfiguration);
$configWriter->save('parcellab/payload_configuration/client_language_code', $languageCodesConfiguration);
