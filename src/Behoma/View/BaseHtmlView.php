<?php
namespace Behoma\View;

use Behoma\Core\LiteralManager;
use Hoimi\Response\Html;

abstract class BaseHtmlView extends Html
{
    use HtmlHelper;
    private $charset = 'utf8';
    private $literalManager = null;

    public function __construct(LiteralManager $literalManager)
    {
        $this->literalManager = $literalManager;
    }

    public function assignWord($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->assign($string);
    }

    public function assignMessage($key)
    {
        $string = $this->literalManager->buildMessage($key);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->assign($string);
    }

    public function writeWord($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key);
        }
        $this->writeHTML($string);
    }

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

    public function writeContent($key, $params = null)
    {
        $string = $this->literalManager->get($key, $params);
        if (!$string) {
            throw new \RuntimeException('no key in dictionary:' . $key . ', ', $this->literalManager->getLanguage());
        }
        $this->writeHTML($this->toHalfContent($string));
    }
    
    
    public function getContent()
    {
        $templateName = $this->getTemplatePath();
        $actionForm = method_exists($this, 'getActionForm') ? $this->getActionForm() : null;
        $response = $this;
        ob_start();
        include $templateName;
        return mb_convert_encoding(ob_get_clean(), $this->getCharset(), 'UTF8');
    }

    public function getTemplatePath()
    {
        return str_replace(
            array($this->nameSpaceRoot(), '\\', $this->responseDirectoryName()),
            array($this->appRoot(), DIRECTORY_SEPARATOR, $this->templateDirectoryName()),
            get_class($this)
        ) . '.php';
    }

    public abstract function nameSpaceRoot();
    public abstract function responseDirectoryName();
    public abstract function appRoot();
    public abstract function templateDirectoryName();
    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
}

