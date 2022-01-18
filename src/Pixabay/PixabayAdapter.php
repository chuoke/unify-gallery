<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Pixabay;

use Chuoke\UnifyGallery\GalleryAdapter;
use Chuoke\UnifyGallery\GalleryAdapterTrait;
use Chuoke\UnifyGallery\GalleryResponse;

/**
 * @see https://pixabay.com/api/docs
 */
class PixabayAdapter implements GalleryAdapter
{
    use GalleryAdapterTrait;

    protected string $name = 'pixabay';

    protected string $baseUri = 'https://pixabay.com/api/';

    protected string $apiKey;

    /**
     * @var PixabayQueryParams|null
     */
    protected $params;

    public function __construct(string $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param  PixabayQueryParams|array  $params
     * @return static
     */
    public function setParams($params)
    {
        $this->params = is_array($params) ? new PixabayQueryParams($params) : $params;

        return $this;
    }

    public function getParams()
    {
        return $this->params ?? ($this->params = new PixabayQueryParams([]));
    }

    /**
     * @param PixabayQueryParams|array $params
     */
    public function get($params = null): GalleryResponse
    {
        if ($params) {
            $this->setParams($params);
        }

        if ($this->isVideo()) {
            return $this->getVideos();
        }

        return $this->getImages();
    }

    public function getVideos(): GalleryResponse
    {
        $response = $this->client()->get(
            'videos',
            [
                'query' => $this->buildParams(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['hits'] ?? [],
            new PixabayFormatter($this),
            $data['total'] > ($this->getParams()->per_page * $this->getParams()->page),
            true
        );
    }

    public function getImages(): GalleryResponse
    {
        $response = $this->client()->get(
            '',
            [
                'query' => $this->buildParams(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['hits'] ?? [],
            new PixabayFormatter($this),
            $data['total'] > ($this->getParams()->per_page * $this->getParams()->page)
        );
    }

    public function isVideo(): bool
    {
        return $this->getParams()->video;
    }

    public function buildParams(): array
    {
        return array_merge($this->getParams()->build() ?? [], ['key' => $this->apiKey]);
    }

    protected function getErrorMessage($error): string
    {
        return '';
    }
}
