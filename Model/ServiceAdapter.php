<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Model;

class ServiceAdapter implements \CreativeStyle\ParcellabIntegration\Api\ServiceAdapterInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    public function __construct(
        \GuzzleHttp\Client $client,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->client = $client;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function performRequestToApi(
        \CreativeStyle\ParcellabIntegration\Api\RequestPreprocessorInterface $requestPreprocessor,
        array $args = [],
        bool $track = false
    ): bool {
        try {
            $args = array_merge($requestPreprocessor->getHeaders(), $requestPreprocessor->preparePayload($args));
            $response = $this->client->request(
                $requestPreprocessor->getRequestMethod(),
                $requestPreprocessor->getEndPointUrl($track),
                $args
            );
            if ($response->getStatusCode() === \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED) {
                return true;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        return false;
    }
}
