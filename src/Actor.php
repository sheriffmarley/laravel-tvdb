<?php

namespace musa11971\TVDB;

use Carbon\Carbon;

class Actor {
    public $id;
    public $name;
    public $role;
    public $image;
    public $imageURL;
    public $lastUpdated;

    public function __construct($data) {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->role = $data->role;
        $this->image = $data->image;
        $this->imageURL = TVDB::IMAGE_URL_PREFIX . $this->image;
        $this->lastUpdated = Carbon::parse($data->lastUpdated);
    }
}