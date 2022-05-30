<?php

namespace CreativeStyle\ParcellabIntegration\Helper;

class Store
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function getStoresOptionsArray(): array
    {
        $stores = $this->storeManager->getStores();

        $options = [];

        foreach ($stores as $store) {
            $options[] = ['label' => sprintf('%s', $store->getCode()), 'value' => $store->getId()];
        }

        return $options;
    }
}
