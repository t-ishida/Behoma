<?php
namespace Behoma\Core;

class Session extends \Hoimi\Session
{
    /**
     * @var Bindable[]
     */
    private $listener = array();
    private $flushed = false;

    public function __construct($request, $config)
    {
        parent::__construct($request, $config);
        $this->flushed = false;
    }

    public function bind(Bindable $obj)
    {
        $obj->setSessionContent($this->query($obj->getSessionKey(), array()));
        $this->listener[] = $obj;
    }

    public function query($key, $default = null)
    {
        if (!isset($key)) {
            throw new \InvalidArgumentException('$key is undefined');
        }
        $array = $_SESSION;
        if (isset($array[$key])) return $array[$key];
        $tmp = $array;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($tmp) || !isset($tmp[$segment])) {
                $tmp = null;
                break;
            } elseif (isset($tmp[$segment])) {
                $tmp = $tmp[$segment];
            } else {
                $tmp = null;
                break;
            }
        }
        return isset($tmp) ? $tmp : $default;
    }

    public function __destruct()
    {
        if (!$this->flushed) {
            $this->flush();
        }
    }

    public function flush()
    {
        foreach ($this->listener as $bindable) {
            $this->set($bindable->getSessionKey(), $bindable->getSessionContent());
        }
        $this->flushed = true;
        parent::flush();
    }

    public function set($key, $val)
    {
        if ($this->flushed) {
            throw new \RuntimeException('calling "set" after flushed');
        }
        if (is_object($key)) {
            $key = serialize($key);
        }
        if (is_scalar($key) && strpos($key, '.') === false) {
            parent::set($key, $val);
        } else {
            if (is_string($key)) {
                $key = explode('.', $key);
            }
            $_SESSION = $this->copyRecursive($_SESSION, $key, $val);
        }
        return $this;
    }

    public function copyRecursive($array, $keys, $value)
    {
        is_array($array) || $array = [];
        $key = array_shift($keys);
        $array[$key] = $keys ? $this->copyRecursive(isset($array[$key]) ?: array(), $keys, $value) : $value;
        return $array;
    }

    public function isFlushed()
    {
        return $this->flushed;
    }
}