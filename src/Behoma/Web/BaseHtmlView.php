<?php
namespace Behoma\Web;

use Behoma\Core\LiteralManager;
use Hoimi\Response\Html;

/**
 * Class BaseHtmlView
 * @package Behoma\Web
 */
abstract class BaseHtmlView extends Html
{
    use HtmlHelper;
    private $charset = 'utf8';
    private $literalManager = null;

    /**
     * BaseHtmlView constructor.
     * @param LiteralManager $literalManager
     */
    public function __construct(LiteralManager $literalManager)
    {
        $this->literalManager = $literalManager;
    }

    /**
     * @param $key
     * @param null $params
     */
    public function assignWord($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->assign($string);
    }

    /**
     * @param $key
     */
    public function assignMessage($key)
    {
        $string = $this->literalManager->buildMessage($key);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->assign($string);
    }

    /**
     * @param $key
     * @param null $params
     */
    public function writeWord($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->writeHTML($string);
    }

    /**
     * @param $key
     * @param $date
     */
    public function assignDate($key, $date)
    {
        $string = $this->literalManager->get($key);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $time = null;
        if (!is_numeric($date)) {
            $time = strtotime($date);
        } else {
            $time = $date;
        }
        if (!$time) {
            throw new \RuntimeException('$date is not date format');
        }
        $this->assign(date($string, $time));
    }

    /**
     * @param $key
     * @param null $params
     */
    public function writeContent($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key . ', ', $this->literalManager->getLanguage());
        }
        $this->writeHTML($this->toHalfContent($string));
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        $templateName = $this->getTemplatePath();
        $actionForm = method_exists($this, 'getActionForm') ? $this->getActionForm() : null;
        $response = $this;
        ob_start();
        include $templateName;
        return mb_convert_encoding(ob_get_clean(), $this->getCharset(), 'UTF8');
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return str_replace(
            array($this->nameSpaceRoot(), '\\', $this->responseDirectoryName()),
            array($this->appRoot(), DIRECTORY_SEPARATOR, $this->templateDirectoryName()),
            get_class($this)
        ) . '.php';
    }

    /**
     * @return mixed
     */
    public abstract function nameSpaceRoot();

    /**
     * @return mixed
     */
    public abstract function responseDirectoryName();

    /**
     * @return mixed
     */
    public abstract function appRoot();

    /**
     * @return mixed
     */
    public abstract function templateDirectoryName();

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    
    /**
     * @return LiteralManager|null
     */
    public function getLiteralManager()
    {
        return $this->literalManager;
    }
}

