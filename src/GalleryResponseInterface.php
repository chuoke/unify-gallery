<?php

namespace Chuoke\UnifyGallery;

interface GalleryResponseInterface
{
    public function original(): array;

    /**
     * @return UnifyGalleryItem[]
     */
    public function unified();

    public function hasMore(): bool;

    public function isVideo(): bool;
}
