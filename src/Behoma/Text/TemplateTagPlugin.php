<?php
namespace Behoma\Text;
interface TemplateTagPlugin
{

    public function getPluginType();

    public function getAttrName();

    public function prepareMethod($data);

    public function doMethod($params, $value);
}