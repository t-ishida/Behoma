<?php
namespace Behoma\Core;
interface Bindable
{
    public function setSessionContent(array $content);

    public function getSessionKey();

    public function getSessionContent();
}