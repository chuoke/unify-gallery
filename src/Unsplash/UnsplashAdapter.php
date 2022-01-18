<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Unsplash;

use Chuoke\UnifyGallery\GalleryAdapter;
use Chuoke\UnifyGallery\GalleryAdapterTrait;
use Chuoke\UnifyGallery\GalleryResponse;

/**
 * @see https://unsplash.com/documentation
 */
class UnsplashAdapter implements GalleryAdapter
{
    use GalleryAdapterTrait;

    protected string $name = 'unsplash';

    protected string $baseUri = 'https://api.unsplash.com/';

    protected string $apiKey;

    /**
     * @var UnsplashQueryParams
     */
    protected $params;

    public function __construct(string $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function setApiKey($apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function withHeaders(): array
    {
        return [
            'Authorization' => 'Client-ID ' . $this->apiKey,
        ];
    }

    /**
     * @param UnsplashQueryParams|array $params
     * @return static
     */
    public function setParams($params): static
    {
        $this->params = $this->createParams($params);

        return $this;
    }

    public function getParams(): UnsplashQueryParams
    {
        return $this->params ?? ($this->params = $this->createParams());
    }

    public function createParams($params = null): UnsplashQueryParams
    {
        return $params && $params instanceof UnsplashQueryParams ? $params : new UnsplashQueryParams($params ?: []);
    }

    /**
     * @param UnsplashQueryParams|null $params
     * @return array
     */
    public function get($params = null): GalleryResponse
    {
        if ($params) {
            $this->setParams($params);
        }

        if ($this->determineSearch()) {
            return $this->search();
        }

        return $this->editorialFeed();
    }

    public function editorialFeed(): GalleryResponse
    {
        $response = $this->client()->get(
            'photos',
            [
                'query' => $this->getParams()->build(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data ?: [],
            new UnsplashFormatter($this),
            count($data ?: []) >= $this->getParams()->per_page
        );
    }

    public function search(): GalleryResponse
    {
        $response = $this->client()->get(
            'search/photos',
            [
                'query' => $this->getParams()->build(),
            ]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['results'] ?? [],
            new UnsplashFormatter($this),
            $data['total'] > ($this->getParams()->per_page * $this->getParams()->page)
        );
    }

    protected function determineSearch()
    {
        return $this->getParams()->isForSearch();
    }

    protected function getErrorMessage($error): string
    {
        return $error['errors'][0] ?? '';
    }
}
