<?php
namespace Behoma\Mail;


class Mail extends Encodable
{

    private $subject = null;
    private $headers = null;
    private $contents = array();
    
    private $to = array();
    private $from = null;
    private $cc = array();
    private $bcc = array();

    /**
     * Mail constructor.
     * @param MailAddress $to
     * @param null $subject
     * @param Content $body
     * @param null $from
     */
    public function __construct(MailAddress $to, $subject, Content $body, $from = null)
    {
        $this->to[] = $to;
        $this->subject = $subject;
        $this->contents[] = $body;
        $this->from = $from;
    }

    /**
     * @return null
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function addTo(MailAddress $address)
    {
        $this->to[] = $address; 
    }
    
    public function addCc(MailAddress $address)
    {
        $this->cc[] = $address;
    }

    public function addBcc(MailAddress $address)
    {
        $this->bcc[] = $address;
    }

    public function addContent(Content $attachment)
    {
        $this->contents[] = $attachment;
    }

    public function addHeader($header)
    {
        $this->headers[] = $header; 
    }

    public function send()
    {
        $content = $this->toArray();
        return mail($content['to'], $content['subject'], $content['body'], $content['header'], $content['additionalParameter']);
    }

    public function toArray()
    {

        $body = '';
        if (count($this->contents) === 1) {
            $body = $this->contents[0]->getContent();
        } else {
            // todo: extract to "MultiPartContent Class"
            $boundary = 'bd_' . md5(uniqid(rand()));
            $this->headers[] = 'Content-type: multipart/alternative; boundary="' .  $boundary . '"';
            foreach ($this->contents as $content) {
                $body .=  "--$boundary\n";
                $body .= $content . "\n\n";
            }
            $body .=  "--$boundary--\n";
        }
        $this->cc && $this->headers[]  = 'Cc:' . implode(',', $this->cc);
        $this->bcc && $this->headers[] = 'Bcc:' . implode(',', $this->bcc);
        return array(
            'to' => implode(',', $this->to),
            'subject' => $this->mimeEncode($this->subject),
            'body' => $body,
            'header' => $this->headers ? implode("\n", $this->headers) : null,
            'additionalParameter' => $this->from ? ('-f ' . $this->from) : null,
        );
    }
}