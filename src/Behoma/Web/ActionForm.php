<?php
namespace Behoma\Web;

use Hoimi\Bindable;
use Hoimi\Request;
use Hoimi\Session;

/**
 * Class ActionForm
 * @package Behoma\Web
 */
class ActionForm implements Bindable
{
    use HtmlHelper;

    private $request = null;
    private $session = null;

    private $container = null;
    private $key = null;
    private $source = null;

    const CONTAINER_KEY = 'hoimi.actionContainer';
    const VALUE_KEY = 'values';
    const ERROR_KEY = 'errors';
    const TOKEN_KEY = 'token';

    /**
     * ActionForm constructor.
     * @param Request $request
     * @param Session $session
     * @param null $containerName
     */
    public function __construct(Request $request, Session $session, $containerName = null)
    {
        $this->request = $request;
        $this->session = $session;
        $this->key = $containerName ?: str_replace('\\', '.', get_class($this));
        $session->bind($this);
        if (!$this->container) {
            $this->container = array(
                self::VALUE_KEY => array(),
                self::ERROR_KEY => array(),
                self::TOKEN_KEY => array(),
            );
        }
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formText($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="text" name="' . $this->escape($name) . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formPassword($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="password" name="' . $this->escape($name) . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @return string
     */
    public function formNumber($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="number" name="' . $this->escape($name) . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formEmail($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="email" name="' . $name . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formTel($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="tel" name="' . $name . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formTextArea($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<textarea name="' . $name . '"' . $this->buildAttributes($attributes) . '>' . $this->escape($value) . '</textarea>';
    }

    /**
     * @param $name
     * @param array $attributes
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formHidden($name, $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        echo '<input type="hidden" name="' . $name . '" value="' . $this->escape($value) . '" ' . $this->buildAttributes($attributes) . ' />';
    }

    /**
     * @param $name
     * @param array $attributes
     * @param array $options
     * @param array $attrLabel
     * @param null $filter
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formRadio($name, $attributes = array(), $options = array(), $attrLabel = array(), $filter = null)
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        $buf = '';
        if (!is_array($attrLabel)) {
            $attrLabel = array();
        }
        foreach ($options as $key => $row) {
            $elm = '';
            $elm .= '<input type="radio" id="' . $name . '_' . $key . '" name="' . $name . '" value="' . $this->escape($key) . '" ' . $this->buildAttributes($attributes) . ' ' . (!is_null($value) && $value != '' && $value == $key ? 'checked="checked"' : '') . ' />';
            $elm .= '<label for="' . $name . '_' . $key . '" ' . $this->buildAttributes($attrLabel) . '>' . $this->escape($row) . '</label>';
            if ($filter && $filter instanceof \Closure) $elm = $filter($elm);
            else                                        $elm .= '&nbsp;';
            $buf .= $elm;
        }
        echo $buf;
    }

    /**
     * @param $name
     * @param array $attributes
     * @param array $options
     * @param array $attrLabel
     * @param null $filter
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formCheckBox($name, $attributes = array(), $options = array(), $attrLabel = array(), $filter = null)
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        $buf = '';
        if (!is_array($attrLabel)) {
            $attrLabel = array();
        }
        foreach ($options as $key => $row) {
            $elm = '';
            $elm .= '<input type="checkbox" id="' . $this->escape($name . '_' . $key) . '" name="' . $this->escape($name . (count($options) > 1 ? '[]' : '')) . '" value="' . $this->escape($key) . '" ' . $this->buildAttributes($attributes) . ' ' . (is_array($value) && in_array($key, $value) ? 'checked="checked"' : '') . ' />';
            $elm .= '<label for="' . $name . '_' . $key . '" ' . $this->buildAttributes($attrLabel) . '>' . $this->escape($row) . '</label>';
            if ($filter && $filter instanceof \Closure) $elm = $filter($elm);
            else                                        $elm .= '&nbsp;';
            $buf .= $elm;
        }
        echo $buf;
    }

    /**
     * @param $name
     * @param array $attributes
     * @param array $options
     * @throws \InvalidArgumentException
     * @return string
     */
    public function formSelect($name, $attributes = array(), $options = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        $value = $this->getActionFormValue($name);
        $buf = '';
        $buf .= '<select name="' . $this->escape($name) . '" ' . $this->buildAttributes($attributes) . '>';
        foreach ($options as $key => $row) {
            if (is_array($value)) {
                $selected = "";
                foreach ($value as $val) {
                    if ($val == $key) {
                        $selected = ' selected="selected"';
                        break;
                    }
                }
                $buf .= '<option value="' . $this->escape($key) . '"' . $selected . '>' . $this->escape($row) . '</option>';
            } else {
                $buf .= '<option value="' . $this->escape($key) . '"' . (!is_null($value) && $value != '' && $value == $key ? ' selected="selected"' : '') . '>' . $this->escape($row) . '</option>';
            }
        }
        $buf .= '</select>';
        echo $buf;
    }

    /**
     * @param string $name
     * @param string $label
     * @param array $attributes
     */
    public function formSubmit($name = 'submit', $label = 'submit', $attributes = array())
    {
        if (!$name) {
            throw new \InvalidArgumentException('name required');
        }
        echo '<input type="submit" name="' . $this->escape($name) . '" value="' . $this->escape($label) . '" ' . $this->buildAttributes($attributes) . ' />';
    }


    /**
     * @param $name
     * @throws \InvalidArgumentException
     * @return null
     */
    public function getActionFormValue($name)
    {
        $value = null;
        if (!preg_match('#^([^\[]+)\[.+?\]#', $name, $tmp)) {
            return $this->get($name);
        }
        if (!$this->container[self::VALUE_KEY][trim($tmp[1])]) {
            return null;
        }
        $form = $this->container[self::VALUE_KEY][trim($tmp[1])];
        $name = preg_replace('#^[^][]+#', '', $name);

        for ($i = 0, $len = mb_strlen($name, 'UTF8'); $i < $len; $i++) {
            $char = mb_substr($name, $i, 1, 'UTF8');
            if (preg_match('#\s#', $char)) continue;
            if ($char == "[") {
                $buf = '';
                for ($i++; $i < $len; $i++) {
                    $char = mb_substr($name, $i, 1);
                    if ($char == "]") {
                        $form = $form[$buf];
                        break;
                    } else {
                        $buf .= $char;
                    }
                }
            } else {
                throw new \InvalidArgumentException ('Syntax Error');
            }
        }
        return $form;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->container[self::VALUE_KEY][$name])) {
            return $this->container[self::VALUE_KEY][$name];
        } else if (isset($this->source[$name])) {
            return $this->source[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function set($name, $value)
    {
        $this->container[self::VALUE_KEY][$name] = $value;
    }

    /**
     * 
     */
    public function clear()
    {
        $this->container = array();
    }

    /**
     * @param $values
     * @throws \InvalidArgumentException
     */
    public function setSource($values)
    {

        if (is_array($values)) {
            $this->source = $values;
        } elseif ($values instanceof \stdClass) {
            $this->source = (array)$values;
        } else {
            throw new \InvalidArgumentException('InvalidType');
        }
    }

    /**
     * @param null $container
     */
    public function setContainer($container)
    {
        $this->container[self::VALUE_KEY] = $container;
    }

    /**
     * @return null
     */
    public function getContainer()
    {
        return $this->container[self::VALUE_KEY];
    }

    /**
     * @param string $key
     * @return null
     */
    public function getErrors($key = null)
    {
        if ($key) {
            $tmp = null;
            if (is_string($key)) {
                $tmp = $this->container[self::ERROR_KEY];
                foreach (explode('.', $key) as $index) {
                    if (isset($tmp[$index])) {
                        $tmp = $tmp[$index];
                    } else {
                        $tmp = null;
                        break;
                    }
                }
            }
            return $tmp;
        }
        return $this->container[self::ERROR_KEY];
    }

    /**
     * @param $name
     * @return bool
     */
    public function isValid($name)
    {
        return !$this->getErrors($name);
    }

    /**
     * 
     */
    public function saveRequest()
    {
        $this->container[self::VALUE_KEY] = $this->request->getBody();
    }
    
    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->container[self::ERROR_KEY] = $errors;
    }

    /**
     * @param $name string 
     * @param $action string 
     * @param $method 
     * @param bool $multipart 
     * @param array $attributes 
     * @throws \InvalidArgumentException
     * @return string 
     */
    public function formStart($name, $action, $method, $multipart = false, $attributes = array())
    {
        if (!$name) throw new \InvalidArgumentException('name required');
        $str = '<form name="' . $this->escape($name) . '"';
        $str .= ' method="' . $this->escape($method) . '"';
        $str .= ' action="' . $this->escape($action) . '"';
        if ($multipart) $str .= ' enctype="multipart/form-data"';
        if ($attributes) $str .= ' ' . $this->buildAttributes($attributes);
        $str .= '>';
        echo $str;
    }

    /**
     * @return string
     */
    public function formEnd()
    {
        $this->set('token', $this->generateToken());
        $this->formHidden('token');
        echo '</form>';
    }


    /**
     * @return bool
     */
    public function verifyToken()
    {
        $result = false;
        if (isset($this->container[self::TOKEN_KEY])) {
            $token = $this->container[self::TOKEN_KEY];
            unset($this->container[self::TOKEN_KEY]);
            $result = $this->request->get('token') === $token;
        }
        return $result;
    
    /**
     * @return mixed
     */
    public function generateToken()
    {
        return $this->container[self::TOKEN_KEY] = sha1(uniqid(mt_rand(), true));
    }

    /**
     * @param null $key
     */
    public function delete($key = null)
    {
        if ($key === null) {
            $this->container[self::VALUE_KEY] = array();
        } else {
            unset($this->container[self::VALUE_KEY][$key]);
        }
    }

    /**
     * @param null $key
     */
    public function deleteErrors($key = null)
    {
        if ($key === null) {
            $this->container[self::ERROR_KEY] = array();
        } else {
            unset($this->container[self::ERROR_KEY][$key]);
        }
    }

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return self::CONTAINER_KEY . '.' . $this->key;
    }

    /**
     * @param array $content
     */
    public function setSessionContent(array $content)
    {
        $this->container = $content;
    }

    /**
     * @return array|null
     */
    public function getSessionContent()
    {
        return $this->container;
    }
}
