<?php
namespace Behoma\Web;

use Hoimi\Response;

abstract class BaseHtmlResponse extends BaseHtmlView implements Response
{
    public function getHeaders()
    {
        return array('ContentType: text/html; charset=UTF-8');
    }
}