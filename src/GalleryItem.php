<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

use JsonSerializable;

/**
 *
 * @method string id()
 * @method string source()
 * @method string type()
 * @method string title()
 * @method string[] tags()
 * @method string copyrighter()
 * @method string copyrightLink()
 * @method GallertyItemLink original()
 * @method GallertyItemLink preview()
 * @method GallertyItemLink[] urls()
 * @method string|null ext()
 * @method string|null color()
 * @method string|int|null duration()
 * @method string|null poster()
 */
class GalleryItem implements JsonSerializable
{
    public function __construct(
        protected string|int $id,
        protected string $source,
        protected string $type,
        protected string $title,
        protected string $copyrighter,
        protected string $copyright_link,
        protected GalleryItemLink $original,
        protected GalleryItemLink $preview,
        protected array $urls,
        protected ?array $tags = [],
        protected ?string $ext = null,
        protected ?string $color = null,
        protected ?string $duration = null,
        protected ?string $poster = null,
    ) {
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'type' => $this->type,
            'title' => $this->title,
            'tags' => $this->tags,
            'copyrighter' => $this->copyrighter,
            'copyright_link' => $this->copyright_link,
            'ext' => $this->ext,
            'original' => $this->original,
            'preview' => $this->preview,
            'color' => $this->color,
            'duration' => $this->duration,
            'urls' => $this->urls,
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
