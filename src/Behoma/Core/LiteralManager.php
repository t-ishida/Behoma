<?php
namespace Behoma\Core;


use Behoma\Text\TemplateTag;
use Hoimi\ArrayContainer;

/**
 * Class LiteralManager
 * @package Behoma\Core
 */
class LiteralManager extends ArrayContainer
{

    private $textTemplate = null;

    /**
     * LiteralManager constructor.
     * @param null $arg
     */
    public function __construct($arg = null)
    {
        $root = array();
        if (is_array($arg)) {
            $root = $arg;
        } elseif (is_file($arg)) {
            $basePath = dirname($arg);
            $dir = opendir($basePath);
            while ($fn = readdir($dir)) {
                $path = $basePath . DIRECTORY_SEPARATOR . $fn;
                if ($fn === '.' || $fn === '..' || $path === $arg) continue;
                $info = pathinfo($fn);
                $root[$info['filename']] = require $path;
            }
        }
        $this->textTemplate = new TemplateTag();
        parent::__construct($root);
        $this->setDictionary($root);
    }

    /**
     * @param $key
     * @param null $params
     * @param null $default
     * @return array|mixed|null|string
     */
    public function get($key, $params = null, $default = null)
    {
        $string = parent::get($key, $default);
        if (!$string || !is_scalar($string)) {
            return $default;
        } elseif (!$params) {
            return $string;
        } else {
            $this->textTemplate->clear();
            $this->textTemplate->setContent($string);
            return $this->textTemplate->evalTag($params);
        }
    }

    /**
     * @param array $dictionary
     * @return $this
     */
    public function setDictionary(array $dictionary)
    {
        $this->array = $dictionary;
        if (!isset($this->array['hoimi']['message'])) {
            $this->array['hoimi']['message'] = $this->getDefaultMessages();
        }
        return $this;
    }

    /**
     * @return array|null
     */
    public function getDictionary()
    {
        return $this->array;
    }

    /**
     * @return array
     */
    public function getDefaultMessages()
    {
        return array(
            'template' => array(
                'INVALID_TYPE' => '<#0>で入力してください',
                'NOT_REQUIRED' => '必ず入力してください',
                'INVALID_RANGE' => '<#1>から<#2>の範囲の<#0>で入力してください',
                'INVALID_FORMAT' => '<#0>の形式で入力してください',
            ),
            'literal' => array(
                'DATE' => '日付',
                'STRING' => '文字',
                'DOUBLE' => '少数',
                'INT' => '整数',
            ),
        );
    }

    /**
     * @param $message
     * @return array|mixed|null|string
     */
    public function buildMessage($message)
    {
        $elements = explode('@', $message);
        $key = 'hoimi.message.template.' . array_shift($elements);
        $params = array_map(function ($key) {
            return $this->get('hoimi.message.literal.' . $key, null, $key);
        }, $elements);
        return $this->get($key, $params);
    }

    /**
     * @return TemplateTag|null
     */
    public function getTextTemplate()
    {
        return $this->textTemplate;
    }
}