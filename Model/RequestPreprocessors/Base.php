<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors;

abstract class Base implements \CreativeStyle\ParcellabIntegration\Api\RequestPreprocessorInterface
{
    public const PAYLOAD_KEY_USER_ID = 'user';
    public const PAYLOAD_KEY_TOKEN = 'token';

    /**
     * @var \CreativeStyle\ParcellabIntegration\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $store;

    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \Magento\Store\Api\Data\StoreInterface $store
    ) {
        $this->configuration = $configuration;
        $this->store = $store;
    }

    public function getHeaders(): array
    {
        return [
            \GuzzleHttp\RequestOptions::HEADERS => [
                "Content-Type" => 'application/json',
                self::PAYLOAD_KEY_USER_ID => $this->configuration->getUserId(),
                self::PAYLOAD_KEY_TOKEN => $this->configuration->getToken()
            ]
        ];
    }

    public function getRequestMethod(): string
    {
        return static::REQUEST_METHOD;
    }
}
