<?php
namespace Behoma\Web;


trait ActionFormValidatable
{

    public function validate($request) 
    {
        $validationResult = \Hoimi\Validator::validate(
            $request,
            $this->getValidatorDefinitions()
        );
        if ($validationResult) {
            $this->getActionForm()->saveRequest();
            $this->getActionForm()->setErrors($validationResult);
            throw new ErrorRedirectException($this->formUrl());
        } 
    }
    
    public abstract function getValidatorDefinitions();
    public abstract function formUrl();
    public abstract function getActionForm();
}
