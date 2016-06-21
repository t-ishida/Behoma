<?php
namespace Behoma\Core;


use Behoma\View\ActionForm;

trait ActionFormBuilder
{
    /**
     * @return ActionForm
     */
    public function getActionForm()
    {
        static $form;
        if (!$form) {
            $class = $this->getActionFormName();
            $form = new $class($this->getRequest(), $this->getSession());
        }
        return $form;
    }

    public abstract function getActionFormName();

    public abstract function getRequest();

    public abstract function getSession();
}