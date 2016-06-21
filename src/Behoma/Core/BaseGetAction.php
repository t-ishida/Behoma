<?php
namespace Behoma\Core;

use Hoimi\BaseAction;
use Hoimi\Request;

abstract class BaseGetAction extends BaseAction
{
    use ActionFormBuilder;

    public function get()
    {
        $response = $this->doGet($this->getRequest());
        if (method_exists($response, 'setActionForm')) {
            $response->setActionForm($this->getActionForm());
        }
        return $response;
    }

    public abstract function doGet(Request $request);

    public function useSessionVariables()
    {
        return true;
    }
}