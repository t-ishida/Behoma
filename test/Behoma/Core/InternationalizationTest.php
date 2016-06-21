<?php
namespace Behoma\Core;


class InternationalizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Internationalization
     */
    private $target = null;

    public function setUp()
    {
        $this->target = new Internationalization();
        $this->target->setDictionary(array(
            'ja' => array(
                'word1' => 'ja-word1',
                'dict1' => array(
                    'word1' => 'ja-dict1-word1',
                    'template1' => 'ja-dict1-<#HOGE>',
                ),
                'obj' => (object)array(),
            ),
            'en' => array(
                'word1' => 'en-word1',
                'dict1' => array(
                    'word1' => 'en-dict1-word1',
                    'template1' => 'en-dict1-<#HOGE>',
                )
            ),
        ))
        ->setAccept(array('ja', 'en'))
        ->setLanguage('ja')
        ->setDefault('ja');
    }

    public function testBuildMessage()
    {
        $this->assertEquals('必ず入力してください', $this->target->buildMessage('NOT_REQUIRED'));
        $this->assertEquals('日付で入力してください', $this->target->buildMessage('INVALID_TYPE@DATE'));
    }
    public function testGetString()
    {
        $this->assertEquals('ja-word1', $this->target->get('word1'));
    }

    public function testGetStringDefault()
    {
        $this->assertEquals('default', $this->target->get('word2', null, 'default'));
    }
    
    public function testGetStringTemplate()
    {
        $this->assertEquals('ja-dict1-value', $this->target->get('dict1.template1', array('HOGE' => 'value')));
    }
    
    public function testGetStringNotScalarAsNull()
    {
        $this->assertNull($this->target->get('dict1'));
        $this->assertNull($this->target->get('obj'));
    }
    
    public function testGetStringEN()
    {
        $this->target->setLanguage('en');
        $this->assertEquals('en-word1', $this->target->get('word1'));
    }
    
}
