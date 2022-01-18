<?php

namespace Chuoke\UnifyGallery;

class Gallery
{
    public function __construct(
        private GalleryAdapter $adapter,
        private array $config = []
    ) {
    }

    /**
     * @param  array  $params
     * @return GalleryResponseInterface
     * @throws \Exception|GalleryException
     */
    public function get($params = null): GalleryResponseInterface
    {
        return $this->adapter->get($params);
    }
}
