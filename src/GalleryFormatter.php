<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

interface GalleryFormatter
{
    public function format($item): GalleryItem;
}
