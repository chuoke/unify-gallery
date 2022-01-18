<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Pexels;

use Chuoke\UnifyGallery\GalleryAdapterTrait;
use Chuoke\UnifyGallery\GalleryAdapter;
use Chuoke\UnifyGallery\GalleryResponse;
use Chuoke\UnifyGallery\Pexels\PexelsQueryParams;

/**
 * @see https://www.pexels.com/api/documentation/
 */
class PexelsAdapter implements GalleryAdapter
{
    use GalleryAdapterTrait;

    protected string $name = 'pexels';

    protected string $baseUri = 'https://api.pexels.com/v1/';

    protected string $apiKey;

    protected PexelsQueryParams|null $params;

    public function __construct(string $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function hasVideo(): bool
    {
        return true;
    }

    public function searchable(): bool
    {
        return true;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function withHeaders()
    {
        return [
            'Authorization' => $this->apiKey,
            // 'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36',
        ];
    }

    /**
     * @param  PexelsQueryParams|array  $params
     * @return static
     */
    public function setParams($params): static
    {
        $this->params = $this->createParams($params);

        return $this;
    }

    public function getParams(): PexelsQueryParams
    {
        return $this->params ?? ($this->params = $this->createParams());
    }

    public function createParams($params = null): PexelsQueryParams
    {
        return $params && $params instanceof PexelsQueryParams ? $params : new PexelsQueryParams($params ?: []);
    }

    /**
     * @param PexelsQueryParams|array|null $params
     */
    public function get($params = null): GalleryResponse
    {
        if ($params) {
            $this->setParams($params);
        }

        if ($this->getParams()->video) {
            return $this->getVideos();
        }

        return $this->getImages();
    }

    public function getImages(): GalleryResponse
    {
        $response = $this->client()->get(
            $this->determineImageQueryScope(),
            [
                'query' => $this->getParams()->build(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['photos'] ?? [],
            new PexelsFormatter($this),
            $data['total_results'] > ($data['page'] * $data['per_page'])
        );
    }

    public function getVideos(): GalleryResponse
    {
        $response = $this->client()->get(
            $this->determineVideoQueryScope(),
            [
                'query' => $this->getParams()->build(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['videos'] ?? [],
            new PexelsFormatter($this),
            $data['total_results'] > ($data['page'] * $data['per_page']),
            true
        );
    }

    public function determineImageQueryScope(): string
    {
        return $this->getParams()->keywords || $this->getParams()->orientation || $this->getParams()->color
            ? 'search'
            : 'curated';
    }

    public function determineVideoQueryScope(): string
    {
        return $this->getParams()->keywords || $this->getParams()->orientation || $this->getParams()->color
            ? 'videos/search'
            : 'videos/popular';
    }

    public function isVideo(): bool
    {
        return $this->getParams()->video;
    }

    protected function getErrorMessage($error): string
    {
        return $error['error'] ?? '';
    }
}
