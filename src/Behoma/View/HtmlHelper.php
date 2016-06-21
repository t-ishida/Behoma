<?php
namespace Behoma\View;

use Behoma\Net\Uri;

trait HtmlHelper
{

    /**
     * @param $attributes
     * @return string
     */
    public function buildAttributes($attributes)
    {
        $str = '';
        foreach ($attributes as $key => $value) {
            if ($value === '' || $value === 0) {
                continue;
            }
            $str .= ' ' . $key . '="';
            if (is_array($value)) {
                if (preg_match('#^on#', $key)) {
                    $str .= $value;
                } else {
                    $str .= implode(' ', $this->escape($value));
                }
            } else {
                if (preg_match('#^on#', $key)){
                    $str .= $value;
                } else{
                    $str .= $this->escape($value);
                }
            }
            $str .= '"';
        }
        return $str;
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * @param $string
     * @return void
     */
    public function assignJS($string)
    {
        echo json_encode($string);
    }

    public function assignCount($array)
    {
        $this->assignNumber(count($array));
    }

    /**
     * @param $string
     * @return void
     */
    public function assignNumber($string)
    {
        $this->assign(number_format($string));
    }

    /**
     * @param $string
     * @return void
     */
    public function assign($string)
    {
        echo htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * @param $string
     * @return void
     */
    public function assignUrl($string)
    {
        echo urldecode($string);
    }

    /**
     * @param $string
     */
    public function writeHTML($string)
    {
        if (is_scalar($string)) echo $string;
        elseif (!$string) echo '';
        else                        var_dump($string);
    }

    /**
     * @param $string
     * @return mixed|object
     */
    public function toHalfContent($string)
    {
        if (!is_string($string)) {
            return $string;
        }
        $urls = array();
        $string = html_entity_decode($string);
        $string = str_replace("\n", "[[br]]", $string);
        $urlRegEx = 'https?://[0-9a-zA-Z-_\.@/\?&=~\#%+;\,]+';
        $serverDomain = (new Uri($_SERVER['SERVER_NAME']))->getDomainName();
        if (preg_match_all('#\{(' . $urlRegEx . ') +: +(\S+?)\}#', $string, $tmp)) {
            uasort($tmp[1], function ($a, $b) {
                return strlen($a) === strlen($b) ? 0 : (strlen($a) > strlen($b) ? -1 : 1);
            });
            for ($i = 0; $i < count($tmp[1]); $i++) {
                $all = $tmp[0][$i];
                $url = $tmp[1][$i];
                $label = $tmp[2][$i];
                if (preg_match('#[\'"]' . preg_quote($url, '#') . '[\'"]#', $string)) continue;
                $string = str_replace($all, "\x0burl_all_$i\x0b", $string);
                $urls["\x0burl_all_$i\x0b"] = $serverDomain === (new Uri($url))->getDomainName() ? "<a href=\"$url\">$label</a>" : "<a href=\"$url\" target=\"_blank\">$label</a>";
            }
        }
        if (preg_match_all('#(' . $urlRegEx . ')#', $string, $tmp)) {
            uasort($tmp[1], function ($a, $b) {
                return strlen($a) === strlen($b) ? 0 : (strlen($a) > strlen($b) ? -1 : 1);
            });
            for ($i = 0; $i < count($tmp[1]); $i++) {
                $url = $tmp[1][$i];
                if (preg_match('#[\'"]' . preg_quote($url, '#') . '[\'"]#', $string)) continue;
                $string = str_replace($url, "\x0burl_$i\x0b", $string);
                $urls["\x0burl_$i\x0b"] = $serverDomain === (new Uri($url))->getDomainName() ? "<a href=\"$url\">$url</a>" : "<a href=\"$url\" target=\"_blank\">$url</a>";
            }
        }
        $string = $this->escape($string);
        $keys = array_keys($urls);
        $values = array_values($urls);
        $keys[]  = "[[br]]";
        $values[] = "<br />";
        $string = str_replace($keys, $values, $string);
        echo $string;
    }

    /**
     * @param $text
     * @param $count
     * @param string $adding
     * @param bool $stripFlg
     * @return mixed|string
     */
    public function cutTextOverflow($text, $count, $adding = '...', $stripFlg = true)
    {
        $stripFlg && $text = trim(strip_tags($text));
        $text = preg_replace('#\s+#', ' ', $text);
        return $count >= mb_strlen($text, 'UTF-8') ? $text : mb_substr($text, 0, $count, 'UTF-8') . $adding;
    }

}