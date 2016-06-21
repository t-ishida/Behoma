<?php
namespace Behoma\Core;


use Hoimi\Response;

class Redirect implements Response
{

    private $location = null;

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function getHeaders()
    {
        return array(
            'Location: ' . $this->location,
        );
    }

    public function getContent()
    {
        return null;
    }
}