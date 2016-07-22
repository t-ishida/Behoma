<?php
namespace Behoma\Web;


trait ActionFormVerifier
{
    public function verifyToken()
    {
        if (!$this->getActionForm()->verifyToken()) {
            throw new ErrorRedirectException($this->formUrl());
        }
    }
    
    public abstract function formUrl();
    public abstract function getActionForm();
}
