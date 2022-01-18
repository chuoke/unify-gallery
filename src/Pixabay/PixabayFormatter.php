<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Pixabay;

use Chuoke\UnifyGallery\GalleryFormatter;
use Chuoke\UnifyGallery\GalleryItem;
use Chuoke\UnifyGallery\GalleryItemLink;

class PixabayFormatter implements GalleryFormatter
{
    /**
     * @var PixabayAdapter
     */
    protected $gallery;

    public function __construct(PixabayAdapter $gallery)
    {
        $this->gallery = $gallery;
    }

    public function format($image): GalleryItem
    {
        if ($this->gallery->isVideo()) {
            return $this->formatVideo($image);
        }

        return $this->formatImage($image);
    }

    public function formatImage($image): GalleryItem
    {
        $sizeMap = [
            'image' => 'original',
            'fullHD' => 'hd',
            'largeImage' => 'large',
            'webformat' => 'medium',
            'preview' => 'small',
            'vector' => 'vector',
        ];

        $urls = [];

        foreach ($sizeMap as $key => $targetType) {
            $urlKey = $key . 'URL';

            if (!array_key_exists($urlKey, $image)) {
                continue;
            }

            $width = '';
            $height = '';

            if ($key !== 'vector') {
                $$width = $image[$key . 'Width'] ?? null;
                $height = $image[$key . 'Height'] ?? null;
            }

            $urls[$targetType] = new GalleryItemLink(
                url: $image[$urlKey],
                type: $targetType,
                width: $width ? intval($width) : null,
                height: $height ? intval($height) : null,
                size: $image[$key . 'Size'] ?? null,
            );
        }

        return new GalleryItem(
            id: $image['id'],
            source: $this->gallery->name(),
            type: $image['type'],
            tags: array_filter(array_map(fn ($item) => trim($item), explode(',', $image['tags'] ?? ''))),
            title: '',
            copyrighter: $image['user'],
            copyright_link: $image['pageURL'],
            color: '',
            ext: pathinfo($image['imageURL'], PATHINFO_EXTENSION),
            original: $urls['original'] ?? $urls['large'],
            preview: $urls['small'] ?? $urls['medium'],
            urls: array_values($urls),
        );
    }

    public function formatVideo($video)
    {
        $urls = [];

        foreach ($video['videos'] as $type => $val) {
            if (0 >= $val['size']) {
                continue;
            }

            $urls[$type] = new GalleryItemLink(
                url: $val['url'],
                type: $type,
                width: $val['width'],
                height: $val['height'],
                size: $val['size'],
            );
        }

        return new GalleryItem(
            id: $video['id'],
            source: $this->gallery->name(),
            type: $video['type'],
            tags: array_filter(array_map(fn ($item) => trim($item), explode(',', $image['tags'] ?? ''))),
            title: '',
            ext: pathinfo($video['videos'][array_key_first($video['videos'])]['url'], PATHINFO_EXTENSION),
            copyrighter: $video['user'],
            copyright_link: $video['pageURL'],
            original: $urls['original'] ?? $urls['large'],
            preview: $urls['small'] ?? $urls['medium'],
            urls: array_values($urls),
            duration: $video['duration'],
            color: '',
        );
    }
}
