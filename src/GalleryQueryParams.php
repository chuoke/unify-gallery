<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

abstract class GalleryQueryParams implements GalleryQueryParamsInterface
{
    public function __construct(array $params)
    {
        foreach ($params as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    abstract public function build();

    public function toArray(): array
    {
        return (array) $this->build();
    }

    public function localeToISO6391(string $locale): string
    {
        return $locale ? substr($locale, 0, strpos($locale, '-')) : '';
    }
}
