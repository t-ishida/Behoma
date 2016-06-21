<?php
namespace Behoma\Web;

use Hoimi\BaseException;

/**
 * Class ErrorRedirectException
 * @package Behoma\Web
 */
class ErrorRedirectException extends BaseException
{
    private $location = null;

    /**
     * ErrorRedirectException constructor.
     * @param string $location
     */
    public function __construct($location)
    {
        $this->location = $location;
        parent::__construct();
    }

    /**
     * @return Redirect
     */
    public function buildResponse()
    {
        return new Redirect($this->location);
    }
}