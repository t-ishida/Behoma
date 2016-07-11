<?php
namespace Behoma\Mail;


class MailTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    public function setUp()
    {
        $this->markTestSkipped('ignore');
        $this->target = new Mail(
            new MailAddress('ishida@aainc.co.jp'),
            'subject',
            new TextContent('test')
            );
    }
    
    public function testSend()
    {
        $this->target->send();
    }
    
    public function testAttachmentFileSend()
    {
        $this->target->addContent(new AttachmentFile(file_get_contents(__FILE__), 'application/octet-stream', basename(__FILE__)));
        $this->target->send();
    }
}
