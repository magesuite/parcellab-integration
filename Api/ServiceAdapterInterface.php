<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Api;

interface ServiceAdapterInterface
{
    public function performRequestToApi(
        \CreativeStyle\ParcellabIntegration\Api\RequestPreprocessorInterface $requestPreprocessor,
        array $args = [],
        bool $track = false
    ): bool;
}
