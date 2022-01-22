<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Bing;

use Chuoke\UnifyGallery\GalleryFormatter;
use Chuoke\UnifyGallery\GalleryItem;
use Chuoke\UnifyGallery\GalleryItemLink;

class BingFormatter implements GalleryFormatter
{
    /**
     * The current gallery driver.
     *
     * @var \Chuoke\UnifyGallery\Bing\BingAdapter
     */
    protected $gallery;

    public function __construct(BingAdapter $gallery)
    {
        $this->gallery = $gallery;
    }

    public function format($item): GalleryItem
    {
        $titleAndCopyrighter = $this->parseTitleAndCopyrighter($item['copyright']);

        $baseUri = rtrim($this->gallery->baseUri());

        foreach (['url', 'copyrightlink'] as $field) {
            if (! empty($item[$field]) && strpos($item[$field], 'http') !== 0) {
                $item[$field] = implode('/', [$baseUri, ltrim($item[$field], '/')]);
            }
        }

        $width = $height = null;
        $w_h = [];
        preg_match('/_(\d{1,}x\d{1,})./', $item['url'], $w_h);
        if (! empty($w_h[1])) {
            [$width, $height] = explode('x', $w_h[1]);
        }

        $link = new GalleryItemLink(
            url: $item['url'],
            width: $width ? intval($width) : null,
            height: $height ? intval($height) : null,
        );

        return new GalleryItem(
            id: $item['hsh'],
            type: 'image',
            source: $this->gallery->name(),
            title: $titleAndCopyrighter['title'] ?: $item['title'],
            tags: [],
            ext: pathinfo($item['url'], PATHINFO_EXTENSION),
            // for_date: date('Y-m-d', strtotime($item['startdate'])),
            copyrighter: $titleAndCopyrighter['copyrighter'] ?: $item['copyright'],
            copyright_link: $item['copyrightlink'],
            original: $link,
            preview: $link,
            urls: [
                new GalleryItemLink(
                    url: $item['url'],
                    type: 'large',
                ),
            ],
        );
    }

    public function parseTitleAndCopyrighter($str)
    {
        $splited = [];

        $matches = [];

        if (preg_match('/\(©(.*)\)/', $str, $matches) && count($matches) >= 2) {
            $splited = [
                'title' => str_replace($matches[0], '', $str),
                'copyrighter' => $matches[1],
            ];
        }

        return $splited;
    }
}
