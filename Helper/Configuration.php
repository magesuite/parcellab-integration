<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_ENABLED = 'parcellab/general/enabled';
    const XML_PATH_CONFIGURATION_TEST_ENABLED = 'parcellab/general/test_mode_enabled';
    const XML_PATH_CONFIGURATION_API_URL = 'parcellab/general/api_url';
    const XML_PATH_CONFIGURATION_USER_ID = 'parcellab/general/user_id';
    const XML_PATH_CONFIGURATION_TOKEN = 'parcellab/general/token';

    const XML_PATH_CONFIGURATION_AUTO_EXPORT_ENABLED = 'parcellab/auto_export/enabled';
    const XML_PATH_CONFIGURATION_SCHEDULE = 'parcellab/auto_export/schedule';
    const XML_PATH_CONFIGURATION_ALLOWED_STATUSES = 'parcellab/auto_export/allowed_statuses';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
    }

    public function isEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function isTestModeEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_TEST_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function getUserId($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_USER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function getApiUrl($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_API_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function getToken($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_TOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function isAutoExportEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_AUTO_EXPORT_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function getSchedule($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_SCHEDULE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function getAllowedStatuses($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_ALLOWED_STATUSES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }
}
