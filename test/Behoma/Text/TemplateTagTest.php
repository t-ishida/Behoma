<?php
/**
 * Created by PhpStorm.
 * User: ishidatakeshi
 * Date: 2016/06/20
 * Time: 14:41
 */

namespace Behoma\Text;


class TemplateTagTest extends \PHPUnit_Framework_TestCase
{
  public function testFreeStringTest () {
    $tags = new TemplateTag ( '' );
    $this->assertEquals (
      $tags->parseAttr ( 'hoge="fuga"' ) ,
      array ( 'label' => 'hoge', 'value' => 'fuga' )
    );

    $this->assertEquals (
      $tags->parseAttr ( 'hoge   =     "fuga"' ) ,
      array ( 'label' => 'hoge', 'value' => 'fuga' )
    );

    $this->assertEquals (
      $tags->parseAttr ( '"hoge"   =     "f\'u\\"ga"' ) ,
      array ( 'label' => 'hoge', 'value' => 'f\'u"ga' )
      );
  }
  public function testEvalValueAssoc ()
  {
    $tags = new TemplateTag('<#A><#B><#C>');
    $this->assertEquals(
        'ABC',
        $tags->evalTag(array('A' => 'A', 'B' => 'B', 'C' => 'C'))
    );
  }
  
  public function testEvalValueArray ()
  {
    $tags = new TemplateTag('<#0><#1><#2>');
    $this->assertEquals('012', $tags->evalTag(array(0, 1, 2)));   
  }

  public function testEvalIF () {
    $tag = new TemplateTag ( $this->getTestData1() );
    $this->assertEquals ( $tag->evalTag ( array ( 'TEST' => 1 ) ) , 'OK' );

    $tag = new TemplateTag ( $this->getTestData2() );
    $this->assertEquals ( $tag->evalTag ( array ( 'TEST' => 1 ) ) , "OK\n" );
  }

  public function testEvalLoop () {
    $tag = new TemplateTag ( $this->getTestData3() );
    $this->assertEquals ( $tag->evalTag ( array (
      'TEST' => array(
        array ( 'VAL' => 1 ),
        array ( 'VAL' => 2 ),
        array ( 'VAL' => 3 ),
      )
    )), '123' );

    $tag = new TemplateTag ( $this->getTestData4() );
    $this->assertEquals ( $tag->evalTag ( array (
      'TEST' => array(
        array ( 'VAL' => 1 ),
        array ( 'VAL' => 2 ),
        array ( 'VAL' => 3 ),
      )
    )), "1\n2\n3\n" );

    $tag = new TemplateTag ( $this->getTestData5() );
    $this->assertEquals ( $tag->evalTag ( array (
      'TEST' => array(
        array ( 'VAL' => 0 ),
        array ( 'VAL' => 0 ),
        array ( 'VAL' => 5 ),
      ),
      'GLOBAL' => 1,
    )), "15<HOGE>" );

    $tag = new TemplateTag ( $this->getTestData5() );
    $this->assertEquals ( $tag->evalTag ( array (
      'TEST' => array(
        1,2,3,4,
      ),
      'GLOBAL' => 1,
    )), "11<HOGE>12<HOGE>13<HOGE>14<HOGE>" );
  }

  public function testIleagalTag () {
    $tag = new TemplateTag ( $this->getTestData6() );
    $this->assertEquals (
      $tag->evalTag ( array (
        'TEST' => array(
          array ( 'VAL' => 5 ),
        ))),
      "<#/IF_HOGE><#/LOOP_HOGE>\n"
    );
  }

  public function testElse () {
    $tag = new TemplateTag ( $this->getTestData7 () );
    $this->assertEquals (
      $tag->evalTag ( array (
        'TRUE'  => true,
        'FALSE' => false,
      )),
      "TRUE:OK\n" .
      "\n\n".
      "FALSE:OK\n"
    );

    $tag = new TemplateTag ( $this->getTestData8 () );
    $this->assertEquals (
      $tag->evalTag ( array (
        'TRUE'  => true,
        'FALSE' => false,
      )),
      "TRUE:OK\n" .
      "\n".
      "FALSE:OK\n"
    );
  }

  public function getTestData1 () { ob_start () ?>
<#IF_TEST>OK<#/IF_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData2 () { ob_start () ?>
<#IF_TEST>
OK
<#/IF_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData3 () { ob_start () ?>
<#LOOP_TEST><#VAL><#/LOOP_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData4 () { ob_start () ?>
<#LOOP_TEST>
<#VAL>
<#/LOOP_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData5 () { ob_start () ?>
<#LOOP_TEST>
<#IF_VAL><#GLOBAL><#VAL><HOGE><#/IF_VAL>
<#/LOOP_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData6 () { ob_start () ?>
<#LOOP_TEST>
<#/IF_HOGE><#/LOOP_HOGE>
<#/LOOP_TEST>
<?php
    return ob_get_clean();
  }

  public function getTestData7 () { ob_start () ?>
<#IF_TRUE>
TRUE:OK
<#ELSE>
TRUE:NG
<#/IF_TRUE>

<#IF_FALSE>
FALSE:NG
<#ELSE>
FALSE:OK
<#/IF_FALSE>
<?php
    return ob_get_clean();
  }

  public function getTestData8 () { ob_start () ?>
<#IF_TRUE>
TRUE:OK
<#IF_FALSE>
FALSE:NG
<#ELSE>
FALSE:OK
<#/IF_FALSE>
<#ELSE>
TRUE:NG
<#/IF_TRUE>
<?php
    return ob_get_clean();
  }
}


  
