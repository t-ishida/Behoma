<?php
namespace Behoma\Web;

use Hoimi\BaseAction;
use Hoimi\Request;

/**
 * Class BasePostAction
 * @package Behoma\Web
 */
abstract class BasePostAction extends BaseAction
{
    use ActionFormValidatable;
    use ActionFormVerifier;
    
    /**
     * @return mixed
     * @throws ErrorRedirectException
     */
    public function post()
    {
        $request =$this->getRequest();
        $this->verifyToken();
        $this->validate($request);
        $response = $this->doPost($request);
        if (!($response instanceof Redirect)) {
            throw new \RuntimeException('post method can return only redirect response.');
        }
        $this->getActionForm()->clear();
        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public abstract function doPost(Request $request);

    /**
     * @return bool
     */
    public function useSessionVariables()
    {
        return true;
    }
}