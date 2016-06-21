<?php
/**
 * Created by PhpStorm.
 * User: ishidatakeshi
 * Date: 2016/06/20
 * Time: 17:00
 */

namespace Behoma\View;


use Behoma\Core\LiteralManager;

class BaseHtmlViewTest extends \PHPUnit_Framework_TestCase
{
    private $target;

    protected function setUp()
    {
        $this->target = \Phake::partialMock('Behoma\View\BaseHtmlView', new LiteralManager());
        $_SERVER['SERVER_NAME'] = 'aainc.co.jp';
    }

    function testBuildAttributes()
    {
        // 空文字や数値の0は無視される
        $attributes = array('hoge' => 'fuga', 'onClick' => 'foo', 'max' => '', 'min' => '0', 'test' => 0);
        $this->assertEquals(
            ' hoge="fuga" onClick="foo" min="0"',
            $this->target->buildAttributes($attributes));
    }
    function testToHalfContent()
    {
        $this->expectOutputString('<a href="http://hoge.hoge.com" target="_blank">fugafuga</a><br /><a href="http://xyzzy.xyzzy.xyzzy" target="_blank">http://xyzzy.xyzzy.xyzzy</a>');
        $this->target->toHalfContent("{http://hoge.hoge.com : fugafuga}\nhttp://xyzzy.xyzzy.xyzzy");
    }
}

