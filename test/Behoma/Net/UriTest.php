<?php
/**
 * Created by PhpStorm.
 * User: ishidatakeshi
 * Date: 2016/06/20
 * Time: 14:07
 */

namespace Behoma\Net;


class UriTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    public function setUp()
    {
        $this->target = new Uri('http://aainc.co.jp/index.html?a=b&z=y&c=d');
    }
    
    public function testNormalize()
    {
        
    }
    
    public function testGetDomain()
    {

    }
    
    public function testIsValid()
    {

    }
}
