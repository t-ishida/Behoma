<?php
namespace Behoma\Text;
interface TemplateTagPlugin
{

    /**
     * @return mixed
     */
    public function getPluginType();

    /**
     * @return mixed
     */
    public function getAttrName();

    /**
     * @param $data
     * @return mixed
     */
    public function prepareMethod($data);

    /**
     * @param $params
     * @param $value
     * @return mixed
     */
    public function doMethod($params, $value);
}