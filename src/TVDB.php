<?php

namespace musa11971\TVDB;

use Illuminate\Support\Facades\Cache;

class TVDB {
    /*
     * Cache key used to store JWT token
     */
    const TOKEN_CACHE_KEY = 'tvdb_jwt_token';

    /*
     * How many minutes to cache the JWT token for
     * Currently a TVDB token is valid for 24 hours
     */
    const TOKEN_CACHE_TIME = 1440;

    /*
     * How many episodes does TVDB return per page
     */
    const EPISODES_PER_PAGE = 100;

    /*
     * TVDB API base URL
     */
    const API_URL = 'https://api.thetvdb.com';

    /*
     * Types of images
     */
    const IMAGE_TYPE_FANART = 'fanart';
    const IMAGE_TYPE_POSTER = 'poster';
    const IMAGE_TYPE_SEASON = 'season';
    const IMAGE_TYPE_SERIES = 'series';

    /*
     * Image URL prefix
     */
    const IMAGE_URL_PREFIX = 'https://www.thetvdb.com/banners/';

    /**
     * Searches for series
     *
     * @param array|string $options
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     *
     * @return array
     */
    public static function search($options) {
        // Format search query
        $query = [];

        if((is_array($options) && isset($options['title'])))
            $query['name'] = $options['title'];
        else if(is_string($options))
            $query['name'] = $options;

        if(is_array($options) && isset($options['imdbId'])) $query['imdbId'] = $options['imdbId'];
        if(is_array($options) && isset($options['zap2itId'])) $query['zap2itId'] = $options['zap2itId'];

        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/search/series'),
            'query'     => $query,
            'auth'      => true,
            'name'      => 'search_series'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        $responseData = (isset($response->json()->data)) ? $response->json()->data : [];
        $series = [];

        foreach($responseData as $seriesData)
            $series[] = new Series($seriesData);

        return $series;
    }

    /**
     * Retrieves a series' details
     *
     * @param integer $id
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     *
     * @return Series
     */
    public static function getSeries($id) {
        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/series/' . $id),
            'auth'      => true,
            'name'      => 'get_series'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        return new Series($response->json()->data);
    }

    /**
     * Retrieves a series' actor details
     *
     * @param integer $id
     * @throws Exceptions\TVDBNotFoundException
     * @throws Exceptions\TVDBUnauthorizedException
     *
     * @return array
     */
    public static function getSeriesActors($id) {
        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/series/' . $id . '/actors'),
            'auth'      => true,
            'name'      => 'get_series_actors'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        $returnData = [];

        foreach($response->json()->data as $actorData)
            $returnData[] = new Actor($actorData);

        return $returnData;
    }

    /**
     * Retrieves a series' images
     *
     * @param integer $id
     *
     * @return array
     * @throws Exceptions\TVDBNotFoundException
     * @throws Exceptions\TVDBUnauthorizedException
     */
    public static function getSeriesImages($id, $type) {
        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/series/' . $id . '/images/query'),
            'query'     => ['keyType' => $type],
            'auth'      => true,
            'name'      => 'get_series_images'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        $returnData = [];

        foreach($response->json()->data as $imageData)
            $returnData[] = [
                'resolution'    => $imageData->resolution,
                'image'         => self::IMAGE_URL_PREFIX . $imageData->fileName,
                'image_thumb'   => self::IMAGE_URL_PREFIX . $imageData->thumbnail
            ];

        return $returnData;
    }

    /**
     * Retrieves a series' episode details
     *
     * @param integer $id
     * @param int $page
     *
     * @return EpisodeCollection
     * @throws Exceptions\TVDBNotFoundException
     * @throws Exceptions\TVDBUnauthorizedException
     */
    public static function getSeriesEpisodes($id, $page = 1) {
        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/series/' . $id . '/episodes'),
            'query'     => ['page' => $page],
            'auth'      => true,
            'name'      => 'get_series_episodes'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        $returnData = [];

        foreach($response->json()->data as $episodeData)
            $returnData[] = new Episode($episodeData);

        return new EpisodeCollection($page, $returnData);
    }

    /**
     * Retrieves an episodes details
     *
     * @param integer $id
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     *
     * @return Episode
     */
    public static function getEpisode($id) {
        $response = self::request([
            'method'    => 'GET',
            'url'       => self::apiURL('/episodes/' . $id),
            'auth'      => true,
            'name'      => 'get_episode'
        ]);

        if(!$response->isSuccessful()) $response->throwException();

        return new Episode($response->json()->data);
    }

    /**
     * Returns a valid TVDB token to use for authentication
     *
     * @return null
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     */
    public static function getToken() {
        // Try to to refresh the cached token if there is one
        if(Cache::has(self::TOKEN_CACHE_KEY)) {
            return Cache::get(self::TOKEN_CACHE_KEY);
        }

        // Create the post fields
        $postfields = [
            'apikey'    => config('tvdb.api_key'),
            'userkey'   => config('tvdb.user_key'),
            'username'  => config('tvdb.username')
        ];

        // Make login request
        $loginResponse = self::request([
            'method'        => 'POST',
            'url'           => self::apiURL('/login'),
            'postfields'    => $postfields,
            'name'          => 'login'
        ]);

        // Login may have failed
        if(!$loginResponse->isSuccessful()) $loginResponse->throwException();

        // Successfully logged in
        $token = $loginResponse->json()->token;

        // Cache the token and return it
        Cache::put(self::TOKEN_CACHE_KEY, $token, self::TOKEN_CACHE_TIME);

        return $token;
    }

    /**
     * Internal function used to make requests
     *
     * @param array $options
     * @return TVDBRequestResponse
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     */
    protected static function request($options) {
        $url = $options['url'];

        // Add URL query params
        if(isset($options['query']))
            $url .= '?' . http_build_query($options['query']);

        // Initialize handle
        $curlHandle = curl_init($url);

        // Create headers
        $requestHeaders = ['Content-Type: application/json'];

        // Add authentication header
        $usedToken = null;

        if(isset($options['auth']) && $options['auth'] === true) {
            $usedToken = self::getToken();
            $requestHeaders[] = 'Authorization: ' . 'Bearer ' . $usedToken;
        }

        // Add postfields
        if(isset($options['postfields'])) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($options['postfields']));
        }

        // Make request
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $options['method']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $requestHeaders);
        $response = curl_exec($curlHandle);

        // Get curl info
        $requestInfo = curl_getinfo($curlHandle);

        // Close handle
        curl_close($curlHandle);

        return new TVDBRequestResponse($requestInfo, $response, $usedToken,
            ((isset($options['name'])) ? $options['name'] : '')
        );
    }

    /**
     * Helper to create an API url with an endpoint appended
     *
     * @param string $append
     * @return string
     */
    protected static function apiURL($append = '') {
        return self::API_URL . $append;
    }
}