<?php
namespace Behoma\Net;
/**
 * Class Uri
 * @package Behoma\Net
 */
class Uri
{
    private $url = null;

    /**
     * Uri constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed|null
     */
    public function normalize()
    {
        $url = $this->url;
        if (!$url) return null;
        if (!preg_match('#^https?.+?\?(.+)$#', $url, $tmp)) return $url;
        parse_str($tmp[1], $query);
        $keys = array_keys($query);
        sort($keys);
        $tmp = array();
        foreach ($keys as $key) $tmp[] = urlencode($key) . '=' . urlencode($query[$key]);
        return preg_replace('#\?.+$#', '?' . implode('&', $tmp), $url);
    }

    /**
     * @return mixed
     */
    public function getDomainName()
    {
        $url = $this->url;
        !preg_match('#https?://[^/]+#', $url) && $url .= 'http://' . preg_replace('#^/+#', '', $url);
        $elements = parse_url($url);
        return $elements['host'];
    }

    /**
     * @return string
     */
    public function get()
    {
        return file_get_contents($this->url);
    }

    /**
     * @param $params
     * @return string
     */
    public function post($params)
    {
        $data = http_build_query($params, "", "&");
        $context = array(
            "http" => array(
                "method" => "POST",
                "header" => implode("\r\n", array(
                    "Content-Type: application/x-www-form-urlencoded",
                    "Content-Length: " . strlen($data)
                )),
                "content" => $data
            )
        );
        return file_get_contents($this->url, 0, $context);
    }

    /**
     * @return bool|int
     */
    public function isValid()
    {
        $str = $this->url;
        if (!$str) return true;
        return preg_match('#^https?://[0-9a-zA-Z-_\#:%\.@/\?&=~]+$#', $str);
    }

    /**
     * @return null
     */
    public function toString()
    {
        return $this->url;
    }
}