<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Pexels;

use Chuoke\UnifyGallery\GalleryQueryParams;
use Chuoke\UnifyGallery\GalleryQueryParamsInterface;

class PexelsQueryParams extends GalleryQueryParams implements GalleryQueryParamsInterface
{
    /**
     * Search terms.
     */
    public string $keywords = '';

    /**
     * Minimum photo size.
     * The current supported sizes are:
     *        large(24MP), medium(12MP) or small(4MP).
     */
    public  $size;

    /**
     * Page number to retrieve. (Optional; default: 1)
     */
    public int $page = 1;

    /**
     * Number of items per page. (Optional; default: 10)
     */
    public int $per_page = 10;

    /**
     * Desired photo color.
     * Supported colors:
     *           red, orange, yellow, green, turquoise, blue, violet,
     *           pink, brown, black, gray, white
     *           or any hexidecimal color code (eg. #ffffff).
     *
     * Not for video.
     */
    public string $color = '';

    /**
     * Filter by photo orientation. Optional. (Valid values: landscape, portrait, squarish)
     */
    public string $orientation = '';

    /**
     * The locale of the search you are performing.
     * The current supported locales are:
     *          'en-US' 'pt-BR' 'es-ES' 'ca-ES' 'de-DE' 'it-IT' 'fr-FR'
     *          'sv-SE' 'id-ID' 'pl-PL' 'ja-JP' 'zh-TW' 'zh-CN' 'ko-KR'
     *          'th-TH' 'nl-NL' 'hu-HU' 'vi-VN' 'cs-CZ' 'da-DK' 'fi-FI'
     *          'uk-UA' 'el-GR' 'ro-RO' 'nb-NO' 'sk-SK' 'tr-TR' 'ru-RU'
     */
    public string $locale = '';

    /**
     * Determine if the query is for video.
     */
    public bool $video = false;

    /**
     * @inheritDoc
     */
    public function build()
    {
        return array_filter([
            'query' => $this->keywords,
            'page' => $this->page,
            'per_page' => $this->per_page,
            'locale' => $this->locale,
            'color' => $this->color,
            'orientation' => $this->orientation,
        ]);
    }
}
