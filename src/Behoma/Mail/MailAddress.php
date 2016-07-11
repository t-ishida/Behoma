<?php
namespace Behoma\Mail;


class MailAddress extends Encodable
{
    private $mailAddress = null;
    public function __construct($mailAddress)
    {
       $this->mailAddress = $mailAddress;
    }
    
    public function __toString()
    {
        $address = $this->mailAddress;
        if (!$address) return '';
        if (is_scalar($address)) $address = array($address);
        $buf = array();
        foreach ($address as $str) {
            if (preg_match('#(.+?)<(.+?)>#', $str, $tmp)) {
                $buf[] = $this->mimeEncode($tmp[1]) . '<' . $tmp[2] . '>';
            } else {
                $buf[] = $str;
            }
        }
        return implode(',', $buf);
    }
}