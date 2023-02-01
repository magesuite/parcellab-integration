<?php

namespace CreativeStyle\ParcellabIntegration\Block\Adminhtml\Form\Field;

class ClientCountryCode extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Store
     */
    protected $storeHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \CreativeStyle\ParcellabIntegration\Helper\Store $storeHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->storeHelper = $storeHelper;
    }

    protected function _prepareToRender()
    {
        $this->addColumn('store', ['label' => __('Store')]);
        $this->addColumn('client_country_code', ['label' => __('Country Code')]);

        $this->_addAfter = false;
    }

    /**
     * @param  string $configurationColumnName
     * @return string
     */
    public function renderCellTemplate($configurationColumnName)
    {
        if ($configurationColumnName == 'store' && isset($this->_columns['store'])) {
            $configurationField = $this->elementFactory->create('select');
            $configurationField->setForm($this->getForm());
            $configurationField->setValues($this->storeHelper->getStoresOptionsArray());
            $configurationField->setName($this->_getCellInputElementName($configurationColumnName));
            $configurationField->setHtmlId($this->_getCellInputElementId('<%- _id %>', $configurationColumnName));

            return str_replace("\n", '', $configurationField->getElementHtml());
        }

        if ($configurationColumnName == 'client_country_code' && isset($this->_columns[$configurationColumnName])) {
            $configurationField = $this->elementFactory->create('text');
            $configurationField->setForm($this->getForm());
            $configurationField->setName($this->_getCellInputElementName($configurationColumnName));
            $configurationField->setHtmlId($this->_getCellInputElementId('<%- _id %>', $configurationColumnName));

            return str_replace("\n", '', $configurationField->getElementHtml());
        }

        return parent::renderCellTemplate($configurationColumnName);
    }
}
