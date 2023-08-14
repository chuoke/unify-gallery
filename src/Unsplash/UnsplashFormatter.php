<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Unsplash;

use Chuoke\UnifyGallery\GalleryFormatter;
use Chuoke\UnifyGallery\GalleryItem;
use Chuoke\UnifyGallery\GalleryItemLink;

class UnsplashFormatter implements GalleryFormatter
{
    /**
     * The current gallery driver.
     *
     * @var UnsplashAdapter
     */
    protected $gallery;

    public function __construct(UnsplashAdapter $gallery)
    {
        $this->gallery = $gallery;
    }

    public function format($image): GalleryItem
    {
        $maps = [
            'raw' => 'original',
            'full' => 'large',
            'regular' => 'medium',
            'thumb' => 'tiny',
        ];

        $urls = [];

        foreach ($image['urls'] as $type => $url) {
            $targetType = $maps[$type] ?? $type;

            $width = '';
            $height = '';

            if ($targetType === 'original' || $targetType === 'large') {
                $width = $image['width'];
                $height = $image['height'];
            } else {
                $w = [];
                $h = [];
                preg_match('/\&w=(\d+)/', $url, $w);
                preg_match('/\&h=(\d+)/', $url, $h);

                $width = $w[1] ?? null;
                $height = $h[1] ?? null;

                if (! $width && $height) {
                    $width = $image['width'] * $height / $image['height'];
                }
                if (! $height && $width) {
                    $height = $image['height'] * $width / $image['width'];
                }
            }

            $urls[$targetType] = new GalleryItemLink(
                url: $url,
                type: $targetType,
                width: $width ? intval($width) : null,
                height: $height ? intval($height) : null,
            );
        }

        return new GalleryItem(
            id: $image['id'],
            source: $this->gallery->name(),
            type: 'photo',
            title: (string) ($image['alt_description'] ?: $image['description']),
            copyrighter: $image['user']['name'],
            copyright_link: $image['links']['html'],
            tags: $image['categories'] ?? [],
            original: $urls['original'] ?? $urls['large'],
            preview: $urls['medium'] ?? $urls['large'],
            color: $image['color'],
            urls: array_values($urls),
        );
    }
}
