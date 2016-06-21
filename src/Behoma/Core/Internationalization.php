<?php
namespace Behoma\Core;

/**
 * Class Internationalization
 * @package Behoma\Core
 */
class Internationalization extends LiteralManager
{
    private $default = null;
    private $accept = null;
    private $language = null;

    /**
     * Internationalization constructor.
     * @param null $arg
     * @param null $accept
     * @param null $language
     * @param null $default
     */
    public function __construct($arg = null, $accept = null, $language = null, $default = null)
    {
        $this->default = $default ?: 'ja';
        $this->accept = $accept ?: array('ja');
        $this->setLanguage($language);
        parent::__construct($arg);
    }

    /**
     * @param $key
     * @param null $params
     * @param null $default
     * @return array|mixed|null|string
     */
    public function get($key, $params = null, $default = null)
    {
        return parent::get($this->language . '.' . $key, $params, $default);
    }

    /**
     * @return null
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        static $fn;
        if (!$fn) {
            $fn = function ($a, $b) {
                if ($a['score'] === $b['score']) return 0;
                return $a['score'] > $b['score'] ? -1 : 1;
            };
        }
        $languages = array();
        foreach (explode(',', $language) as $language) {
            $l = null;
            $s = null;
            if (preg_match('#^(.+?)(?:-.+)?;q=(.+?)$#S', $language, $tmp)) {
                list(, $l, $s) = $tmp;
            } else {
                list($l, $s) = array($language, 1);
            }
            $languages[] = array('language' => $l, 'score' => $s);
        }
        usort($languages, $fn);
        $this->language = null;
        foreach ($languages as $language) {
            if (in_array($language['language'], $this->accept)) {
                $this->language = $language['language'];
                break;
            }
        }
        if (!$this->language) {
            $this->language = $this->default;
        }
        return $this;
    }

    /**
     * @param array $dictionary
     * @return $this
     */
    public function setDictionary(array $dictionary)
    {
        $response = parent::setDictionary($dictionary);
        unset($this->array['hoimi']);
        foreach ($this->array as $key => $dict) {
            if (!isset($dict['hoimi']['message'])) {
                $dict['hoimi']['message'] = $this->getDefaultMessages();
                $this->array[$key] = $dict;
            }
        }
        return $response;
    }

    /**
     * @return array|null
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * @param array $accept
     * @return $this
     */
    public function setAccept(array $accept)
    {
        $this->accept = $accept;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }
}

