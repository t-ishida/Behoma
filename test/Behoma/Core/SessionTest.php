<?php
/**
 * Created by PhpStorm.
 * User: ishidatakeshi
 * Date: 2016/06/20
 * Time: 11:58
 */

namespace Behoma\Core;


use Hoimi\Request;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $target = null;
    private $bindable = null;
    public function setUp()
    {
        $this->target = new Session(new Request(array(),array()), array());
        $_SESSION = array(
            'HOGE' => 1,
            'FUGA' => 2,
            'PIYO' => array(
                'FIZZ'  => 'BUZZ',
            ));
        $this->bindable = \Phake::mock('\Behoma\Core\Bindable');
        \Phake::when($this->bindable)->getSessionKey()->thenReturn('bindable.dummy');
    }

    public function testQuery()
    {
        $this->assertEquals(1, $this->target->query('HOGE'));
    }

    public function testQueryDeeply()
    {
        $this->assertEquals('BUZZ', $this->target->query('PIYO.FIZZ'));
    }
    
    public function testQueryDefault()
    {
        $this->assertEquals('default', $this->target->query('PIYO.FIZZ.BUZZ', 'default'));
    }

    public function testSet()
    {
        $this->target->set('HOGE', 11);
        $this->assertEquals(11, $_SESSION['HOGE']);
    }
    
    public function testSetDeeply()
    {
        $this->target->set('HOGE.FUGA.PIYO', 11);
        $this->assertEquals(array(
            'HOGE' => array('FUGA' => array('PIYO' => 11)),
            'FUGA' => 2,
            'PIYO' => array(
                'FIZZ' => 'BUZZ'
            ),
        ), $_SESSION);
    }
    
    public function testFulsh()
    {
        $this->target->set('bindable.dummy.xyzzy', 'hogefuga');
        $this->target->bind($this->bindable);
        \Phake::when($this->bindable)->getSessionContent()->thenReturn(array(
            'hoge'=> 'hogehoge',
            'xyzzy' => 'piyopiyo',
        ));
        $this->target->flush();
        $this->assertEquals(array(
            'HOGE' => 1 ,
            'FUGA' => 2,
            'PIYO' => array(
                'FIZZ' => 'BUZZ'
            ),
            'bindable' => array(
                'dummy' => array(
                    'hoge' => 'hogehoge',
                    'xyzzy' => 'piyopiyo',
                )                 
            ),
        ), $_SESSION);
    }
}
