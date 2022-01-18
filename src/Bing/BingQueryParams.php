<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery\Bing;

use Chuoke\UnifyGallery\GalleryQueryParams;

class BingQueryParams extends GalleryQueryParams
{
    /**
     * Page number to retrieve. (Optional; default: -1)
     * Map to `idx`, which says which day of data to get
     *  -1 is tomorrow、 0 today 、 1 yesterday ...
     */
    public int $page = 1;

    /**
     * Number of items per page. (Optional; default: 8, max: 8)
     */
    public int $per_page = 8;

    /**
     * The region parameter,
     * the default value is zh-CN,
     * you can also use en-US, ja-JP, en-AU, en-UK, de-DE, en-NZ, en-CA.
     */
    public string $locale = '';

    public function build(): array
    {
        // The maximum is 8
        $this->per_page = min($this->per_page, 8);

        return [
            'format' => 'js',
            'idx' => ($idx = $this->page - 1) > -1 ? $idx : 0,
            'n' => $this->per_page,
            'mkt' => $this->locale,
        ];
    }
}
