<?php
namespace Behoma\Web;


trait ActionFormVerifier
{
    use ActionFormBuilder;
    public function verifyToken()
    {
        if (!$this->getActionForm()->verifyToken()) {
            throw new ErrorRedirectException($this->formUrl());
        }
    }
    
    public abstract function formUrl();
}