<?php

namespace musa11971\TVDB;

use ArrayAccess;
use Countable;
use Iterator;

class EpisodeCollection implements ArrayAccess, Iterator, Countable {
    public $page;
    public $episodes;
    protected $iteratorPosition = 0;

    public function __construct($page, $episodes) {
        $this->page = $page;
        $this->episodes = $episodes;
    }

    /**
     * Checks whether or not the episode collection has a follow-up page
     * .. with more results
     *
     * @return bool
     */
    public function hasNextPage() {
        return (count($this->episodes) >= TVDB::EPISODES_PER_PAGE);
    }

    /**
     * Returns the page number of the next page
     *
     * @return int
     */
    public function nextPage() {
        return ($this->page + 1);
    }

    /** Method from interface Countable */
    public function count()
    {
        return count($this->episodes);
    }

    /** Methods from interface ArrayAccess */
    public function offsetSet($offset, $value) {
        if(is_null($offset)) {
            $this->episodes[] = $value;
        } else {
            $this->episodes[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->episodes[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->episodes[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->episodes[$offset]) ? $this->episodes[$offset] : null;
    }

    /** Methods from interface Iterator */
    public function rewind() {
        $this->iteratorPosition = 0;
    }

    public function current() {
        return $this->episodes[$this->iteratorPosition];
    }

    public function key() {
        return $this->iteratorPosition;
    }

    public function next() {
        ++$this->iteratorPosition;
    }

    public function valid() {
        return isset($this->episodes[$this->iteratorPosition]);
    }
}