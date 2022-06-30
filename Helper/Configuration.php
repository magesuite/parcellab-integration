<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_CONFIGURATION_ENABLED = 'parcellab/general/enabled';
    public const XML_PATH_CONFIGURATION_ORDER_REGISTER_ENABLED = 'parcellab/general/order_register_enabled';
    public const XML_PATH_CONFIGURATION_TEST_ENABLED = 'parcellab/general/test_mode_enabled';
    public const XML_PATH_CONFIGURATION_API_URL = 'parcellab/general/api_url';
    public const XML_PATH_CONFIGURATION_USER_ID = 'parcellab/general/user_id';
    public const XML_PATH_CONFIGURATION_TOKEN = 'parcellab/general/token';

    public const XML_PATH_CONFIGURATION_AUTO_EXPORT_ENABLED = 'parcellab/auto_export/enabled';
    public const XML_PATH_CONFIGURATION_SCHEDULE = 'parcellab/auto_export/schedule';
    public const XML_PATH_CONFIGURATION_ALLOWED_STATUSES = 'parcellab/auto_export/allowed_statuses';

    public const XML_PATH_CONFIGURATION_PAYLOAD_CLIENT_COUNTRY_CODE = 'parcellab/payload_configuration/client_country_code';
    public const XML_PATH_CONFIGURATION_PAYLOAD_CLIENT_LANGUAGE_CODE = 'parcellab/payload_configuration/client_language_code';
    public const XML_PATH_CONFIGURATION_PAYLOAD_COURIER_CODE = 'parcellab/payload_configuration/courier_code';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
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

    public function isOrderRegisterEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_ORDER_REGISTER_ENABLED,
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

    /**
     * @param null $scopeCode
     * @return array
     */
    public function getClientCountryCodes($scopeCode = null): array
    {
        $clientCountryCodes = [];
        $configuration = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_PAYLOAD_CLIENT_COUNTRY_CODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );

        if (!$configuration) {
            return [];
        }

        $decodedConfiguration = json_decode($configuration, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        foreach ($decodedConfiguration as $storeConfiguration) {
            $clientCountryCodes[$storeConfiguration['store']] = $storeConfiguration['client_country_code'];
        }

        return $clientCountryCodes;
    }

    /**
     * @param null $scopeCode
     * @return array
     */
    public function getClientLanguageCodes($scopeCode = null): array
    {
        $clientLanguageCodes = [];
        $configuration = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_PAYLOAD_CLIENT_LANGUAGE_CODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );

        if (!$configuration) {
            return [];
        }

        $decodedConfiguration = json_decode($configuration, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        foreach ($decodedConfiguration as $storeConfiguration) {
            $clientLanguageCodes[$storeConfiguration['store']] = $storeConfiguration['client_language_code'];
        }

        return $clientLanguageCodes;
    }

    /**
     * @param null $scopeCode
     * @return array
     */
    public function getCourierCodes($scopeCode = null): array
    {
        $courierCodes = [];
        $configuration = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIGURATION_PAYLOAD_COURIER_CODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );

        if (!$configuration) {
            return [];
        }

        $decodedConfiguration = json_decode($configuration, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        foreach ($decodedConfiguration as $storeConfiguration) {
            $courierCodes[$storeConfiguration['courier_name']] = $storeConfiguration['courier_code'];
        }

        return $courierCodes;
    }
}
