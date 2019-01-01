<?php

namespace musa11971\TVDB;

use Carbon\Carbon;

class Episode {
    public $id;
    public $imdbId;
    public $name;
    public $image;
    public $season;
    public $number;
    public $synopsis;
    public $firstAired;
    public $lastUpdated;
    public $directors;
    public $writers;
    public $guestStars;
    public $tvdbRating;

    public function __construct($data) {
        $this->id = $data->id;
        $this->imdbId = (strlen($data->imdbId)) ? $data->imdbId : null;
        $this->name = $data->episodeName;

        if($data->filename)
            $this->image = TVDB::IMAGE_URL_PREFIX . $data->filename;
        else
            $this->image = null;

        $this->season = $data->airedSeason;
        $this->number = $data->airedEpisodeNumber;
        $this->synopsis = $data->overview;
        $this->firstAired = Carbon::parse($data->firstAired);
        $this->lastUpdated = Carbon::createFromTimestamp($data->lastUpdated);
        $this->directors = $data->directors;
        $this->writers = $data->writers;
        $this->guestStars = $data->guestStars;
        $this->tvdbRating = [
            'average'   => $data->siteRating,
            'count'     => $data->siteRatingCount
        ];
    }
}