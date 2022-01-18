<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

trait GalleryAdapterTrait
{
    public function baseUri(): string
    {
        return $this->baseUri;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function hasVideo(): bool
    {
        return false;
    }

    public function searchable(): bool
    {
        return false;
    }

    public function withHeaders(): array
    {
        return [];
    }

    protected function client(): Client
    {
        return $this->client ?? $this->client = new Client([
            'base_uri' => $this->baseUri(),
            'headers' => $this->withHeaders(),
            'verify' => false,
        ]);
    }

    /**
     * Parse json from response.
     *
     * @param  ResponseInterface  $response
     *
     * @throws GalleryException
     */
    public function parseJson(ResponseInterface $response): array
    {
        $decoded = json_decode($response->getBody()->getContents(), true);

        if ($decoded === false) {
            throw new \RuntimeException('Failed to decode JSON response:' . json_last_error_msg());
        }

        return $decoded ?: [];
    }

    protected function checkRequestFailed(ResponseInterface $response)
    {
        $status = $response->getStatusCode();

        if ($this->isStatusCodeSuccessful($status)) {
            return;
        }

        $errorMessage = $this->getErrorMessage($this->parseJson($response));

        $this->throwRequestFailedException($errorMessage ?: $response->getBody(), $status);
    }

    protected function isStatusCodeSuccessful($statusCode)
    {
        return $statusCode >= 200 && $statusCode < 300;
    }

    public function throwRequestFailedException($message = '', int $code = 0)
    {
        throw new GalleryRequestFailedException('Failed to get response from ' . $this->name() . ':' . $message, $code);
    }
}
