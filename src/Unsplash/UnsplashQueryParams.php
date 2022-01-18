<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Unsplash;

use Chuoke\UnifyGallery\GalleryQueryParams;

class UnsplashQueryParams extends GalleryQueryParams
{
    /**
     * Search terms.
     */
    public string $keywords = '';

    /**
     * Page number to retrieve. (Optional; default: 1)
     */
    public int $page = 1;

    /**
     * Number of items per page. (Optional; default: 10)
     */
    public int $per_page = 10;

    /**
     * Thers are 2 scope:
     *
     * 1: How to sort the photos when getting a single page from the Editorial feed.
     *    Valid values: latest, oldest, popular; default: popular
     *
     * 2: How to sort the photos when search.
     *    Valid values are latest and relevant. default: relevant
     */
    public string $order_by = '';

    /**
     * Limit results by content safety.
     * Valid values are low and high. (Optional; default: low).
     */
    public string $content_filter = '';

    /**
     * Filter results by color. Optional.
     * Valid values are:
     *       black_and_white, black, white, yellow, orange,
     *       red, purple, magenta, green, teal, and blue.
     */
    public string $color = '';

    /**
     * Filter by photo orientation. Optional. (Valid values: landscape, portrait, squarish)
     */
    public string $orientation = '';

    /**
     *
     * Original params supported ISO 639-1 language code of the query. Optional, default: "en"
     *
     * @see https://unsplash.com/documentation#supported-languages
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     */
    public string $locale = '';

    public function build()
    {
        return [
            'query' => $this->keywords,
            'page' => $this->page,
            'per_page' => $this->per_page,
            'order_by' => $this->order_by ?: ($this->isForSearch() ? '' : 'popular'),
            'content_filter' => $this->content_filter,
            'color' => $this->color,
            'orientation' => $this->orientation,
            'lang' => $this->localeToISO6391($this->locale),
        ];
    }

    public function isForSearch()
    {
        return $this->keywords
            || $this->orientation
            || $this->color
            || $this->content_filter
            || $this->locale;
    }
}
