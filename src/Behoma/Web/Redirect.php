<?php
namespace Behoma\Web;


use Hoimi\Response;

/**
 * Class Redirect
 * @package Behoma\Web
 */
class Redirect implements Response
{

    private $location = null;

    /**
     * Redirect constructor.
     * @param $location
     */
    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return array(
            'Location: ' . $this->location,
        );
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return null;
    }
}