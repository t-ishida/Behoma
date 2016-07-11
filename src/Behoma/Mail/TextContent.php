<?php
namespace Behoma\Mail;


class TextContent extends Content
{

    public function __construct($content)
    {
        parent::__construct($content, 'text/plain');
    }

    public function headers()
    {
        return array(
            "Content-Type: text/plain; charset=\"ISO-2022-JP\"",
            "Content-Transfer-Encoding: 7bit",
        );
    }
}