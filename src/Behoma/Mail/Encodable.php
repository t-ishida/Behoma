<?php
namespace Behoma\Mail;


abstract class Encodable
{
    private $encoding = 'ISO-2022-JP';
    private $realEncoding = 'ISO-2022-JP';
    
    public function mimeEncode($str)
    {
        return '=?' . $this->encoding . '?B?' . base64_encode(mb_convert_encoding($str, 
            ($this->realEncoding ? $this->realEncoding : $this->encoding), 
            'UTF-8')) . '?=';
    }
}