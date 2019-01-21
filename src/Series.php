<?php

namespace musa11971\TVDB;

use Carbon\Carbon;

class Series {
    public $id;
    public $imdbId;
    public $zap2itId;
    public $title;
    public $slug;
    public $alternativeTitles;
    public $banner;
    public $bannerURL;
    public $status;
    public $firstAired;
    public $network;
    public $runtime;
    public $genres;
    public $synopsis;
    public $lastUpdated;
    public $airs;
    public $tvdbRating;
    public $added;
    public $addedBy;

    public function __construct($data) {
        $this->id = $data->id;
        $this->imdbId = @$data->imdbId ?: null;
        $this->zap2itId = @$data->zap2itId ?: null;
        $this->title = @$data->seriesName ?: null;
        $this->slug = @$data->slug ?: null;
        $this->alternativeTitles = @$data->aliases ?: null;
        $this->banner = @$data->banner ?: null;
        $this->bannerURL = (isset($data->banner)) ? TVDB::IMAGE_URL_PREFIX . $data->banner : null;
        $this->status = @$data->status ?: null;
        $this->firstAired = (isset($data->firstAired)) ? Carbon::parse($data->firstAired) : null;
        $this->network = [
            'id'    => @$data->networkId ?: null,
            'name'  => @$data->network ?: null
        ];
        $this->runtime = (isset($data->runtime) && strlen($data->runtime) && is_numeric($data->runtime)) ? (int) $data->runtime : null;
        $this->genres = @$data->genre ?: null;
        $this->synopsis = @$data->overview ?: null;
        $this->lastUpdated = (isset($data->lastUpdated)) ? Carbon::createFromTimestamp($data->lastUpdated) : null;
        $this->airs = [
            'dayOfWeek' => @$data->airsDayOfWeek ?: null,
            'time'      => @$data->airsTime ?: null
        ];
        $this->tvdbRating = [
            'average'   => @$data->siteRating ?: null,
            'count'     => @$data->siteRatingCount ?: null
        ];
        $this->added = @$data->added ?: null;
        $this->addedBy = @$data->addedBy ?: null;
    }

    /**
     * Retrieve the series' actor details
     *
     * @return array
     * @throws Exceptions\TVDBUnauthorizedException
     * @throws Exceptions\TVDBNotFoundException
     */
    public function getActors() {
        return TVDB::getSeriesActors($this->id);
    }

    /**
     * Retrieve the series' episode details
     *
     * @param int $page
     * @return EpisodeCollection
     * @throws Exceptions\TVDBNotFoundException
     * @throws Exceptions\TVDBUnauthorizedException
     */
    public function getEpisodes($page = 1) {
        return TVDB::getSeriesEpisodes($this->id, $page);
    }

    /**
     * Retrieve the series' images
     *
     * @param string $type
     * @return array
     * @throws Exceptions\TVDBNotFoundException
     * @throws Exceptions\TVDBUnauthorizedException
     */
    public function getImages($type) {
        return TVDB::getSeriesImages($this->id, $type);
    }
}