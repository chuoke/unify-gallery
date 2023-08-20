<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

use JsonSerializable;

/**
 * @method string url()
 * @method string|null type()
 * @method string|null width()
 * @method string|null height()
 * @method string|null size()
 */
class GalleryItemLink implements JsonSerializable
{
    public function __construct(
        protected string $url,
        protected ?string $type = null,
        protected ?float $width = null,
        protected ?float $height = null,
        protected ?int $size = null,
    ) {
    }

    public function toArray()
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __call($name, $args)
    {
        $name = strtolower(preg_replace('/[A-Z]/', '_$0', $name));

        return $this->$name ?? null;
    }
}
