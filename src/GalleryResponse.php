<?php

namespace Chuoke\UnifyGallery;

class GalleryResponse implements GalleryResponseInterface
{
    public function __construct(
        protected array $original,
        protected array $items,
        protected GalleryFormatter $formatter,
        protected bool $hasMore,
        protected bool $isVideo = false
    ) {
    }

    public function original(): array
    {
        return $this->original;
    }

    /**
     * @return UnifyGalleryItem[]
     */
    public function unified()
    {
        return array_map(
            function ($item) {
                return $this->formatter->format($item);
            },
            $this->items
        );
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public function isVideo(): bool
    {
        return $this->isVideo;
    }
}
