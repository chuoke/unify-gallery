<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Bing;

use Chuoke\UnifyGallery\GalleryAdapter;
use Chuoke\UnifyGallery\GalleryAdapterTrait;
use Chuoke\UnifyGallery\GalleryResponse;
use GuzzleHttp\Client;

class BingAdapter implements GalleryAdapter
{
    use GalleryAdapterTrait;

    protected string $name = 'bing';

    protected string $baseUri = 'https://bing.com/';

    protected ?BingQueryParams $params;

    protected ?Client $client;

    public function __construct()
    {
        //
    }

    /**
     * @param  BingQueryParams|array  $params
     * @return static
     */
    public function setParams($params): static
    {
        $this->params = $this->createParams($params);

        return $this;
    }

    public function getParams(): BingQueryParams
    {
        return $this->params ?? ($this->params = $this->createParams());
    }

    public function createParams($params = null): BingQueryParams
    {
        return $params && $params instanceof BingQueryParams ? $params : new BingQueryParams($params ?: []);
    }

    /**
     * @param  BingQueryParams|array|null  $params
     * @return array
     */
    public function get($params = null): GalleryResponse
    {
        if ($params) {
            $this->setParams($params);
        }

        $response = $this->client()->get(
            'HPImageArchive.aspx',
            ['query' => $this->buildListQueryParams()]
        );

        $this->checkRequestFailed($response);

        $data = $this->parseJson($response);

        return new GalleryResponse(
            $data,
            $data['images'] ?? [],
            new BingFormatter($this),
            count($data['images'] ?? []) >= $this->getParams()->per_page
        );
    }

    protected function buildListQueryParams()
    {
        // format=js&idx=0&n=8
        return $this->getParams()->build();
    }

    protected function getErrorMessage($error): string
    {
        return '';
    }
}
