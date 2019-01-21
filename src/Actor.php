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
        $this->role = (strlen($data->role)) ? $data->role : null;
        $this->image = (strlen($data->image)) ? $data->image : null;
        $this->imageURL = ($this->image !== null) ? TVDB::IMAGE_URL_PREFIX . $this->image : null;
        $this->lastUpdated = Carbon::parse($data->lastUpdated);
    }
}