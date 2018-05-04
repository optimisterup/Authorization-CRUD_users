<?php

class Route
{
    private $_uri=[];

    /**
     * Builds a collection of internal URL's to look for
     * @param $uri
     */
    public function add($uri)
    {
        $this->_uri[]=$uri;
    }
}