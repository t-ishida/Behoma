<?php
namespace Behoma\Web;



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

    /**
     * @return mixed
     */
    public abstract function getActionFormName();

    /**
     * @return mixed
     */
    public abstract function getRequest();

    /**
     * @return mixed
     */
    public abstract function getSession();
}