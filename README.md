# This a unify collection of Unsplash\Pexels\Pixabay\Bing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chuoke/unify-gallery.svg?style=flat-square)](https://packagist.org/packages/chuoke/unify-gallery)
[![Tests](https://github.com/chuoke/unify-gallery/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/chuoke/unify-gallery/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/chuoke/unify-gallery.svg?style=flat-square)](https://packagist.org/packages/chuoke/unify-gallery)

I try to use Unsplash\Pexels\Pixabay\Bing in the same interactive way to get beautiful works of art suitable for wallpaper.

Please Help me Do Better!

## Installation

You can install the package via composer:

```bash
composer require chuoke/unify-gallery
```

## Usage

```php
$gallery = new Chuoke\UnifyGallery\GalleryManager();

$arts = $gallery->get();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Thanks

[spatie/package-skeleton-php](https://github.com/spatie/package-skeleton-php)

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [chuoke](https://github.com/chuoke)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
