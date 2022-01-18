<?php

declare(strict_types=1);

namespace Chuoke\UnifyGallery;

use function array_merge;

class Config
{
    public function __construct(
        private array $options = []
    ) {
    }

    public function get(string $property, $default = null): mixed
    {
        return $this->options[$property] ?? $default;
    }

    public function extend(array $options): Config
    {
        return new Config(array_merge($this->options, $options));
    }

    public function withDefaults(array $defaults): Config
    {
        return new Config($this->options + $defaults);
    }

    public function all()
    {
        return $this->options;
    }
}
