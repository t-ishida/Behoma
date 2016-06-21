<?php
namespace Behoma\Text;

class TemplateTag
{
    private $data = null;
    private $content = '';
    private $plugins = array();

    public function __construct($content = null, $data = null, $plugins = array())
    {
        if (is_string($content)) {
            $this->content = $content;
        } elseif (is_file($content)) {
            $this->content = mb_convert_encoding(file_get_contents($content), 'UTF8', 'sjis,utf8,euc-jp');
        }
        $this->data = $data;
        $this->plugins = $plugins;
    }

    public function evalTag($currentData = null, $idx = 0, $endData = '', $canEval = 1)
    {
        list ($inTags, $buf, $ret, $stack, $quote) = array(false, '', '', array(), null);
        if (is_null($this->data)) {
            $this->data = $currentData;
        }
        if (is_null($currentData)) {
            $currentData = $this->data;
        }
        for ($i = $idx; $i < mb_strlen($this->content, 'UTF8'); $i++) {
            $char = mb_substr($this->content, $i, 1, 'UTF8');
            if ($quote) {
                if ($char === '\\') {
                    $buf .= $char;
                    $buf .= $this->content[++$i];
                } elseif ($quote === $char) {
                    $quote = null;
                    $buf .= $char;
                } else {
                    $buf .= $char;
                }
            } elseif ($char === '<') {
                $ret .= $buf;
                list ($inTags, $buf) = array(true, '');
            } elseif ($inTags && ($char === '"' || $char === "'")) {
                $quote = $char;
                $buf .= $char;
            } elseif ($inTags && $char === '>') {
                if (preg_match('/^#\/(.+)$/', $buf, $tmp)) {
                    if ($canEval || !$stack) {
                        if ($tmp[1] === $endData) {
                            return $canEval ? array($i, $ret) : array($i, '');
                        } else {
                            $ret .= '<' . $buf . '>';
                        }
                    } elseif ($stack[count($stack) - 1] === $tmp[1]) {
                        array_shift($stack);
                    } else {
                        $ret .= '<' . $buf . '>';
                    }
                } elseif (preg_match('/^#(LOOP|IF)_(.+)$/', $buf, $tmp)) {
                    list ($afterIdx, $str) = array($i, '');
                    $tag = $this->getTagInfo($buf);
                    if ($canEval) {
                        $name = preg_replace('#^.+?_#', '', $tag['tag_name']);
                        if ($tmp[1] === 'LOOP') {
                            list ($afterIdx, $str) = $this->evalLoop($i, $name, $currentData, $tag['attrs']);
                        } elseif ($tmp[1] === 'IF') {
                            list ($afterIdx, $str) = $this->evalIF($i, $name, $currentData, $tag['attrs']);
                        } else {
                            /** LOOPでもIFでもない場合は無視でOK? **/
                        }
                    } else {
                        $stack[] = $tag['tag_name'];
                    }
                    $ret .= $str;
                    $i = $afterIdx;
                } elseif ($buf === '#ELSE') {
                    if ($canEval) {
                        list ($i) = $this->evalTag($currentData, $i, $endData, false);
                        $this->content[$i + 1] === "\n" && $i++;
                    } else {
                        $ret = '';
                        $canEval = true;
                    }
                } elseif (preg_match('/^#/', $buf)) {
                    $tag = $this->getTagInfo($buf);
                    $tagValue = $currentData[preg_replace('/^#/', '', $tag['tag_name'])];
                    foreach ($tag['attrs'] as $row) {
                        if (!$this->plugins['value'][$row['label']]) {
                            continue;
                        }
                        $tagValue = $this->plugins['value'][$row['label']]->doMethod($row['value'], $tagValue);
                    }
                    $ret .= $tagValue;
                } else {
                    $ret .= '<' . $buf . '>';
                }
                $buf = '';
                $inTags = false;
            } else {
                $buf .= $char;
            }
        }
        return !$endData ? ($ret . $buf) : array($i, $ret . $buf);
    }

    public function getTagInfo($chars)
    {
        $inQuote = null;
        $attr = '';
        $elms = array();
        if (!preg_match('#^\#(\S+)( ?.*)$#s', $chars, $tmp)) return $chars;
        $ret = array('tag_name' => $tmp[1], 'attrs' => array());
        for ($i = 0; $i < mb_strlen($tmp[2], 'UTF8'); $i++) {
            $char = mb_substr($tmp[2], $i, 1);
            if ($inQuote) {
                if ($inQuote && $char === '\\') {
                    $attr .= $char . $tmp[2][++$i];
                } elseif ($inQuote === $char) {
                    $inQuote = null;
                    $attr .= $char;
                } else {
                    $attr .= $char;
                }
            } else {
                if (!$inQuote && !trim($char)) {
                    $attr && $elms[] = trim($attr);
                    $attr = '';
                } elseif (!$inQuote && ($char === "'" || $char === '"')) {
                    $inQuote = $char;
                    $attr .= $char;
                } else {
                    $attr .= $char;
                }
            }
        }
        if ($attr) {
            $elms[] = $attr;
        }
        foreach ($elms as $attr) {
            $ret['attrs'][] = $this->parseAttr($attr);
        }
        return $ret;
    }

    public function parseAttr($chars)
    {
        $label = '';
        $inQuote = 0;
        $esc = 0;
        $buf = '';

        for ($i = 0; $i < strlen($chars); $i++) {
            $char = $chars[$i];
            if (!$inQuote && $char === ' ') {
                continue;
            }
            if ($esc === 1) {
                $esc = 0;
                $buf = $buf . $char;
            } else {
                if ($inQuote === $char) {
                    $inQuote = null;
                } else {
                    if ($inQuote) {
                        if ($char === '\\') {
                            $esc = 1;
                        } else {
                            $buf = $buf . $char;
                        }
                    } else {
                        if ($char === "'" || $char === '"') {
                            $inQuote = $char;
                        } elseif ($char === '=') {
                            $label = $buf;
                            $buf = '';
                        } else {
                            $buf = $buf . $char;
                        }
                    }
                }
            }
        }
        $value = $buf;
        return array('label' => $label, 'value' => $value);
    }

    public function evalLoop($idx, $name, $data, $attrs = array())
    {
        $ret = '';
        $after = $idx;
        if (is_array($data[$name])) {
            foreach ($data[$name] as $key => $row) {
                $tmp = $this->data;
                if (!is_array($row)) $row = array('IDX' => $key, 'VAL' => $row);
                foreach ($row as $key2 => $val) {
                    $tmp[$key2] = $val;
                }
                $row = $tmp;
                list ($after, $cont) = $this->evalTag($row, $idx + 1 + (mb_substr($this->content, $idx + 1, 1, 'UTF8') == "\n" ? 1 : 0), 'LOOP_' . $name, 1);
                $ret .= $cont;
            }
        } else {
            list ($after, $cont) = $this->evalTag(array(), $idx + 1 + (mb_substr($this->content, $idx + 1, 1, 'UTF8') == "\n" ? 1 : 0), 'LOOP_' . $name, 0);
        }
        return array($after + 1, $ret);
    }

    public function evalIF($idx, $name, $data, $attrs = array())
    {
        $tmp = $this->data;
        foreach ($data as $key => $val) $tmp[$key] = $val;
        list ($after, $cont) = $this->evalTag(
            $tmp,
            $idx + 1 + (mb_substr($this->content, $idx + 1, 1, 'UTF8') == "\n" ? 1 : 0),
            'IF_' . $name,
            $tmp[$name] ? 1 : 0
        );
        return array($after + 1, $cont);
    }

    public function addPlugin(TemplateTagPlugin $plugin)
    {
        $this->plugins[] = $plugin;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function clear()
    {
        $this->data = null;
        $this->content = null;
    }
}
