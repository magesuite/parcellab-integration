<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Api;

interface RequestPreprocessorInterface
{
    public function getEndPointUrl(bool $track = false): string;

    public function preparePayload(array $args = []): array;

    public function getHeaders(): array;

    public function getRequestMethod(): string;
}
