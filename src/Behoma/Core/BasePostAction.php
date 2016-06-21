<?php
namespace Behoma\Core;

use Hoimi\BaseAction;

abstract class BasePostAction extends BaseAction
{
    use ActionFormBuilder;

    public function post()
    {
        $validationResult = \Hoimi\Validator::validate(
            $this->getRequest(),
            $this->getValidatorDefinitions()
        );
        if ($validationResult) {
            $this->getActionForm()->setErrors($validationResult);
            throw new ErrorRedirectException($this->formUrl());
        }
        $response = $this->doPost();
        if (!($response instanceof Redirect)) {
            throw new \RuntimeException('post method can return only redirect response.');
        }
        $this->getActionForm()->clear();
        return $response;
    }

    public abstract function getValidatorDefinitions();

    public abstract function formUrl();

    public abstract function doPost();

    public function useSessionVariables()
    {
        return true;
    }
}