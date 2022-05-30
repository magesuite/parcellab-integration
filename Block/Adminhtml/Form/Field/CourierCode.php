<?php

namespace CreativeStyle\ParcellabIntegration\Block\Adminhtml\Form\Field;

class CourierCode extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var \Creativestyle\CustomizationLensplaza\Block\Adminhtml\Form\Field\DeliveryTimeMatrixRenderer\Pool
     */
    protected $renderersPool;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Creativestyle\CustomizationLensplaza\Block\Adminhtml\Form\Field\DeliveryTimeMatrixRenderer\Pool $renderersPool
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Creativestyle\CustomizationLensplaza\Block\Adminhtml\Form\Field\DeliveryTimeMatrixRenderer\Pool $renderersPool,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->renderersPool = $renderersPool;
    }

    protected function _prepareToRender()
    {
        $this->addColumn('courier_name', ['label' => __('Courier Name')]);
        $this->addColumn('courier_code', ['label' => __('Courier Code')]);

        $this->_addAfter = false;
    }

    /**
     * @param  string $configurationColumnName
     * @return string
     */
    public function renderCellTemplate($configurationColumnName)
    {
        if ($configurationColumnName == 'courier_name' && isset($this->_columns['courier_name'])) {
            $configurationField = $this->elementFactory->create('text');
            $configurationField->setForm($this->getForm());
            $configurationField->setName($this->_getCellInputElementName($configurationColumnName));
            $configurationField->setHtmlId($this->_getCellInputElementId('<%- _id %>', $configurationColumnName));

            return str_replace("\n", '', $configurationField->getElementHtml());
        }

        if ($configurationColumnName == 'courier_code' && isset($this->_columns[$configurationColumnName])) {
            $configurationField = $this->elementFactory->create('text');
            $configurationField->setForm($this->getForm());
            $configurationField->setName($this->_getCellInputElementName($configurationColumnName));
            $configurationField->setHtmlId($this->_getCellInputElementId('<%- _id %>', $configurationColumnName));

            return str_replace("\n", '', $configurationField->getElementHtml());
        }

        return parent::renderCellTemplate($configurationColumnName);
    }
}
