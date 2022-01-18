<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

interface GalleryAdapter
{
    public function name(): string;

    public function hasVideo(): bool;

    public function searchable(): bool;

    public function get(GalleryQueryParamsInterface|array $params = null): GalleryResponseInterface;
}
