<?php
namespace Behoma\Net;
class Uri
{
    private $url = null;

    public function __construct($url)
    {
        $this->url = $url;
    }

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

    public function getDomainName()
    {
        $url = $this->url;
        !preg_match('#https?://[^/]+#', $url) && $url .= 'http://' . preg_replace('#^/+#', '', $url);
        $elements = parse_url($url);
        return $elements['host'];
    }

    public function get()
    {
        return file_get_contents($this->url);
    }

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

    public function isValid()
    {
        $str = $this->url;
        if (!$str) return true;
        return preg_match('#^https?://[0-9a-zA-Z-_\#:%\.@/\?&=~]+$#', $str);
    }

    public function toString()
    {
        return $this->url;
    }
}