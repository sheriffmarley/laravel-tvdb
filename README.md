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

## Configuration
The publish command above will publish a `tvdb.php` config file to your Laravel config folder. Be sure to tweak the values with your personal API details.  
I recommend not touching the config file, but rather defining your API details in your project's `.env` file, like so:  
```
TVDB_API_KEY=ETIO2B4NO372XP0X
TVDB_USER_KEY=XXUXCXR8LYXUNM7P
TVDB_USERNAME=musa11971
```
Don't forget to clear recache your config. (`php artisan config:cache`)

## Usage
### Finding series by ID
The `getSeries` function returns a `Series` object.
```php
// Find a series by its TVDB ID
// ID: 73739 (Lost)
$result = TVDB::getSeries(73739);

echo $result->title; // "Lost"
```

### Searching for series
The `search` function returns an array of `Series` objects, or an empty array if no results are found.
```php
// Search by title
$results = TVDB::search('Planet Earth');

// Search by IMDB ID
$results = TVDB::search(['imdbId' => 'tt5491994']);

// Search by zap2it ID
$results = TVDB::search(['zap2itId' => 'SH303483']);
```

### Getting series images
In order get an array of a series' images, you need to specify the type of image you'd like to retrieve. Available types are listed below.
```php
/*
 * Get the images of the series by TVDB ID
 * ID: 73739 (Lost)
 *
 * Available image types:
 * - TVDB::IMAGE_TYPE_FANART
 * - TVDB::IMAGE_TYPE_POSTER
 * - TVDB::IMAGE_TYPE_SEASON
 * - TVDB::IMAGE_TYPE_SERIES
 */
$images = TVDB::getSeriesImages(73739, TVDB::IMAGE_TYPE_POSTER);

// Or get the images directly from a "Series" object
$series = TVDB::getSeries(73739);
$images = $series->getImages(TVDB::IMAGE_TYPE_FANART);
```

### Getting series actors
The following options are available for retrieving an array of actors.
```php
/*
 * Get the actors of the series by TVDB ID
 * ID: 73739 (Lost)
 */
$actors = TVDB::getSeriesActors(73739);

// Or get the actors directly from a "Series" object
$series = TVDB::getSeries(73739);
$actors = $series->getActors();
```

### Getting series episodes
The TVDB API endpoint for retrieving episodes is paginated. This means that you will need to specify a page number when retrieving episodes.
```php
/*
 * Get the episodes of the series by TVDB ID
 * ID: 73739 (Lost)
 *
 * The second parameter specifies the page (page 1 by default)
 */
$episodes = TVDB::getSeriesEpisodes(73739, 2);

// Or get the episodes directly from a "Series" object
$series = TVDB::getSeries(73739);

$episodes = $series->getEpisodes(2);
```
Example 1 - iterating over all episodes:
```php
$page = 1;

do {
    $episodes = TVDB::getSeriesEpisodes(73739, $page);

    echo "Page $page has " . count($episodes) . " episodes. <br />";

    $page++;
} while($episodes->hasNextPage());

/*
 * Output:
 * Page 1 has 100 episodes.
 * Page 2 has 49 episodes.
 */
```
Example 2:
```php
$episodes = TVDB::getSeriesEpisodes(73739);

foreach($episodes as $episode) {
    echo $episode->name . '<br />';
}
```

### Getting individual episodes
```php
/*
 * Retrieve the episode with ID 127131
 * .. returns an "Episode" object
 */
$episode = TVDB::getEpisode(127131);

echo $episode->name; //  "Pilot (1)"
```

### Getting your TVDB JWT token
Sometimes it can be useful to retrieve your TVDB JWT token (for testing the API, for example). 
```php
echo TVDB::getToken();
```

## Credits

- [Musa Semou](https://github.com/musa11971)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
