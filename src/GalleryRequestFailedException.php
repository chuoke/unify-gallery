<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

use RuntimeException;

class GalleryRequestFailedException extends RuntimeException implements GalleryException
{
    public function __construct(string $message = 'Unify gallery request failed.', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
