<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Pexels;

use Chuoke\UnifyGallery\GalleryFormatter;
use Chuoke\UnifyGallery\GalleryItem;
use Chuoke\UnifyGallery\GalleryItemLink;
use Chuoke\UnifyGallery\Pexels\PexelsAdapter;

class PexelsFormatter implements GalleryFormatter
{
    /**
     * The current gallery driver.
     *
     * @var PexelsAdapter
     */
    protected $gallery;

    public function __construct(PexelsAdapter $gallery)
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
        $urls = [];

        foreach ($image['src'] as $type => $url) {
            $width = '';
            $height = '';

            if ($type === 'original') {
                $width = $image['width'];
                $height = $image['height'];
            } else {
                $w = [];
                $h = [];
                preg_match('/\&w=(\d+)/', $url, $w);
                preg_match('/\&h=(\d+)/', $url, $h);

                $width = $w[1] ?? null;
                $height = $h[1] ?? null;

                if (!$width && $height) {
                    $width = $image['width'] * $height / $image['height'];
                }
                if (!$height && $width) {
                    $height = $image['height'] * $width / $image['width'];
                }
            }

            $urls[$type] = new GalleryItemLink(
                url: $url,
                type: $type,
                width: $width ? intval($width) : null,
                height: $height ? intval($height) : null,
            );
        }

        return new GalleryItem(
            id: $image['id'],
            source: $this->gallery->name(),
            title: $image['alt'] ?? '',
            type: 'photo',
            ext: pathinfo($image['src']['original'], PATHINFO_EXTENSION),
            tags: [],
            copyrighter: $image['photographer'],
            copyright_link: $image['url'],
            color: $image['avg_color'],
            original: $urls['original'],
            preview: $urls['medium'] ?? $urls['large'],
            urls: array_values($urls),
        );
    }

    public function formatVideo($video): GalleryItem
    {
        $urls = [];

        foreach ($video['video_files'] as $url) {
            $urls[] = new GalleryItemLink(
                url: $url['link'],
                type: $url['quality'],
                width: $url['width'],
                height: $url['height'],
            );
        }

        return new GalleryItem(
            id: $video['id'],
            source: $this->gallery->name(),
            type: 'video',
            title: '',
            ext: pathinfo($video['video_files'][0]['link'], PATHINFO_EXTENSION),
            tags: [],
            copyrighter: $video['user']['name'],
            copyright_link: $video['url'],
            original: $urls[0],
            preview: $urls[1],
            urls: $urls,
            color: $video['avg_color'] ?? null,
            duration: $video['duration'],
            poster: $video['image'] ?: $video['video_pictures'][0],
        );
    }
}
