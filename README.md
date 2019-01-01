# Laravel TVDB API wrapper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/musa11971/laravel-tvdb.svg?style=flat-square)](https://packagist.org/packages/musa11971/laravel-tvdb)
[![Quality Score](https://img.shields.io/scrutinizer/g/musa11971/laravel-tvdb.svg?style=flat-square)](https://scrutinizer-ci.com/g/musa11971/laravel-tvdb)
[![Total Downloads](https://img.shields.io/packagist/dt/musa11971/laravel-tvdb.svg?style=flat-square)](https://packagist.org/packages/musa11971/laravel-tvdb)

The `musa11971/laravel-tvdb` package provides easy to use functions that help you interact with the TVDB API.

## Installation

You can install the package via composer:

``` bash
composer require musa11971/laravel-tvdb
```

Publish the config file with the following artisan command:
```bash
php artisan vendor:publish --provider="musa11971\TVDB\TVDBServiceProvider"
```

The command above will publish a `tvdb.php` config file to your Laravel config folder. Be sure to tweak the values with your personal API details.

## Credits

- [Musa Semou](https://github.com/musa11971)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
