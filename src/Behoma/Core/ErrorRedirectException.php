<?php
namespace Behoma\Core;

use Hoimi\BaseException;

class ErrorRedirectException extends BaseException
{
    private $location = null;

    public function __construct($location)
    {
        $this->location = $location;
        parent::__construct();
    }

    public function buildResponse()
    {
        return new Redirect($this->location);
    }
}