<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\Config\Storage\WriterInterface $configWriter */
$configWriter = $objectManager->get(\Magento\Framework\App\Config\Storage\WriterInterface::class);

$configWriter->delete('parcellab/payload_configuration/client_country_code');
$configWriter->delete('parcellab/payload_configuration/client_language_code');
