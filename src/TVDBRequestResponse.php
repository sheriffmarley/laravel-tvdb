<?php

namespace musa11971\TVDB;

use musa11971\TVDB\Exceptions\TVDBNotFoundException;
use musa11971\TVDB\Exceptions\TVDBUnauthorizedException;

class TVDBRequestResponse {
    protected $info;
    protected $response;
    protected $usedToken;
    protected $name;

    /**
     * TVDBRequestResponse constructor.
     *
     * @param array $info
     * @param string $response
     * @param string|null $usedToken
     * @param string|null $name
     */
    public function __construct($info, $response, $usedToken, $name = null)
    {
        $this->info = $info;
        $this->response = $response;
        $this->usedToken = $usedToken;
        $this->name = $name;
    }

    /**
     * Gets the response in JSON format
     *
     * @return mixed
     */
    public function json() {
        return json_decode($this->response);
    }

    /**
     * Checks whether or not the TVDB request was successful
     *
     * @return bool
     */
    public function isSuccessful() {
        if($this->info['http_code'] === 200)
            return true;
        else return false;
    }

    /**
     * Throws the appropriate exception if the response was not successful
     *
     * @throws TVDBUnauthorizedException
     * @throws TVDBNotFoundException
     */
    public function throwException() {
        // Unauthorized
        if($this->info['http_code'] === 401) {
            if($this->name == 'login') {
                throw new TVDBUnauthorizedException('401: Unable to retrieve a TVDB token with the given details. Check your config.');
            }
            else {
                throw new TVDBUnauthorizedException(
                    '401: Unauthorized request to ' . $this->info['url'] . '. ' .
                    (($this->usedToken === null) ? '(no TVDB token was found)' : '(however a TVDB token was found)')
                );
            }
        }
        // Not found
        else if($this->info['http_code'] === 404) {
            if($this->name == 'get_series') {
                throw new TVDBNotFoundException('404: Unable to find the series - ' . $this->info['url']);
            }
            else if($this->name == 'get_series_actors') {
                throw new TVDBNotFoundException('404: Unable to find the actors - ' . $this->info['url']);
            }
            else if($this->name == 'get_series_episodes') {
                throw new TVDBNotFoundException('404: Unable to find the episodes - ' . $this->info['url']);
            }
            else if($this->name == 'get_episode') {
                throw new TVDBNotFoundException('404: Unable to find the episode - ' . $this->info['url']);
            }
            // Generic 404 exception
            // We're ignoring 'search_series' because this API endpoint returns 404
            // .. when there are no search results - we don't want that.
            else if($this->name != 'search_series') {
                throw new TVDBNotFoundException('404: Not found. ' . $this->info['url']);
            }
        }
    }
}