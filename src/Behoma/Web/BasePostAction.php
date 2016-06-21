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
    use ActionFormBuilder;

    /**
     * @return mixed
     * @throws ErrorRedirectException
     */
    public function post()
    {
        $request =$this->getRequest(); 
        $validationResult = \Hoimi\Validator::validate(
            $request,
            $this->getValidatorDefinitions()
        );
        if ($validationResult) {
            $this->getActionForm()->saveRequest();
            $this->getActionForm()->setErrors($validationResult);
            throw new ErrorRedirectException($this->formUrl());
        }
        $response = $this->doPost($request);
        if (!($response instanceof Redirect)) {
            throw new \RuntimeException('post method can return only redirect response.');
        }
        $this->getActionForm()->clear();
        return $response;
    }

    /**
     * @return mixed
     */
    public abstract function getValidatorDefinitions();

    /**
     * @return mixed
     */
    public abstract function formUrl();

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