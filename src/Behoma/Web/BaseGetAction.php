<?php
namespace Behoma\Web;

use Hoimi\BaseAction;
use Hoimi\Request;

/**
 * Class BaseGetAction
 * @package Behoma\Web
 */
abstract class BaseGetAction extends BaseAction
{
    use ActionFormBuilder;

    /**
     * @return mixed
     */
    public function get()
    {
        $response = $this->doGet($this->getRequest());
        if (method_exists($response, 'setActionForm')) {
            $response->setActionForm($this->getActionForm());
        }
        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public abstract function doGet(Request $request);

    /**
     * @return bool
     */
    public function useSessionVariables()
    {
        return true;
    }
}