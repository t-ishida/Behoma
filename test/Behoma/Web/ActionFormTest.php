<?php
namespace Behoma\Web;


use Hoimi\Request;
use Hoimi\Session;

class ActionFormTest extends \PHPUnit_Framework_TestCase
{
    private $target;
    private $request;
    private $session;

    protected function setUp()
    {
        $_SESSION = array();
        $this->request = new Request(array(),array());
        $this->session = new Session($this->request, array());
        $this->target = new ActionForm($this->request, $this->session);
    }

    public function testGetActionFormValue () {
        /** @var ActionForm $actionForm */
        $actionForm = $this->target;
        $actionForm->setContainer(array (
            'HOGE' => 'xyzzy',
            'array' => array (
                array (
                    'named' => array (
                        'array' => array ( 1, 2, 3 )
                    )
                ),
                array (
                    'named' => array (
                        'fizz' => 'buzz',
                        'hoge' => 'fuga',
                    )
                ),
            ),
            'named' => array(
                'array' => array ( 1000, 2000, 3000 ),
                'named' => array (
                    'array' => array( 100,200, 300 ),
                ),
            )
        ));
        $this->assertEquals ( 'xyzzy', $actionForm->getActionFormValue ( 'HOGE' ) );
        $this->assertEquals ( 1, $actionForm->getActionFormValue ( 'array[0][named][array][0]' ) );
        $this->assertEquals ( 'buzz', $actionForm->getActionFormValue ( 'array[1][named][fizz]' ) );
        $this->assertEquals ( 'fuga', $actionForm->getActionFormValue ( 'array[1][named][hoge]' ) );
        $this->assertEquals ( 1000, $actionForm->getActionFormValue ( 'named[array][0]' ) );
        $this->assertEquals ( 2000, $actionForm->getActionFormValue ( 'named[array][1]' ) );
        $this->assertEquals ( 3000, $actionForm->getActionFormValue ( 'named[array][2]' ) );
        $this->assertEquals ( 100, $actionForm->getActionFormValue ( 'named[named][array][0]' ) );
        $this->assertEquals ( 200, $actionForm->getActionFormValue ( 'named[named][array][1]' ) );
        $this->assertEquals ( 300, $actionForm->getActionFormValue ( 'named [named]
      [array]
      [2]' ) );
        $this->assertEquals ( null, $actionForm->getActionFormValue ( 'そんなものは無い' ) );

        try {
            $actionForm->getActionFormValue ( 'named[array]a[bc]d' );
        } catch ( \Exception $e ) {
            $this->assertEquals ( 'Syntax Error', $e->getMessage () );
        }
    }

    function testFormText()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<input type="text" name="hoge" value="value"  />');
        $target->formText('hoge');
    }

    function testFormPassword()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<input type="password" name="hoge" value="value"  />');
        $target->formPassword('hoge');
    }

    function testFormNumber()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('1'));
        $this->expectOutputString('<input type="number" name="hoge" value="1"  min="0" max="100" />');
        $target->formNumber('hoge', array('min' => "0", 'max' => "100"));
    }

    function testFormEmail()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<input type="email" name="hoge" value="value"  />');
        $target->formEmail('hoge');
    }

    function testFormTel()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<input type="tel" name="hoge" value="value"  />');
        $target->formTel('hoge');
    }

    function testFormTextArea()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<textarea name="hoge">value</textarea>');
        $target->formTextArea('hoge');
    }

    function testFormHidden()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('value'));
        $this->expectOutputString('<input type="hidden" name="hoge" value="value"  />');
        $target->formHidden('hoge');
    }

    function testFormRadio()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('piyo'));

        $attribute = array('class' => 'class1 class2');
        $options = array('foo' => 'bar', 'piyo' => 'fuga');
        $attributeLabel = array('style' => 'color: #888;');

        $html  = '<input type="radio" id="hoge_foo" name="hoge" value="foo"  class="class1 class2"  />';
        $html .= '<label for="hoge_foo"  style="color: #888;">bar</label>&nbsp;';
        $html .= '<input type="radio" id="hoge_piyo" name="hoge" value="piyo"  class="class1 class2" checked="checked" />';
        $html .= '<label for="hoge_piyo"  style="color: #888;">fuga</label>&nbsp;';
        $this->expectOutputString($html);
        $target->formRadio('hoge', $attribute, $options, $attributeLabel);
    }

    function testFormCheckbox()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue(array('piyo')));

        $attribute = array('class' => 'class1 class2');
        $options = array('foo' => 'bar', 'piyo' => 'fuga');
        $attributeLabel = array('style' => 'color: #888;');

        $html  = '<input type="checkbox" id="hoge_foo" name="hoge[]" value="foo"  class="class1 class2"  />';
        $html .= '<label for="hoge_foo"  style="color: #888;">bar</label>&nbsp;';
        $html .= '<input type="checkbox" id="hoge_piyo" name="hoge[]" value="piyo"  class="class1 class2" checked="checked" />';
        $html .= '<label for="hoge_piyo"  style="color: #888;">fuga</label>&nbsp;';
        $this->expectOutputString($html);
        $target->formCheckBox('hoge', $attribute, $options, $attributeLabel);
    }

    function testFormSelect()
    {
        $target = $this->createMock(array('getActionFormValue'));
        $target->expects($this->once())->method('getActionFormValue')->will($this->returnValue('piyo'));

        $attributes = array('class' => 'class1 class2');
        $options = array('foo' => 'bar', 'piyo' => 'fuga');

        $html  = '<select name="hoge"  class="class1 class2">';
        $html .= '<option value="foo">bar</option>';
        $html .= '<option value="piyo" selected="selected">fuga</option>';
        $html .= '</select>';

        $this->expectOutputString($html);
        $target->formSelect('hoge', $attributes, $options);
    }

    function testFormSubmit()
    {
        $target = $this->createMock(array(''));
        $attributes = array('class' => 'class1 class2');

        $html = '<input type="submit" name="xyzzy" value="送信"  class="class1 class2" />';
        $this->expectOutputString($html);
        $target->formSubmit('xyzzy', '送信', $attributes);
    }


    function createMock($method = array())
    {
        return $this->getMockForAbstractClass(
            '\Behoma\Web\ActionForm',
            array(),
            'ActionForm',
            false,
            false,
            false,
            $method
        );
    }
}
