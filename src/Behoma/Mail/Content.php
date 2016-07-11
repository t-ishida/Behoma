<?php
namespace Behoma\Mail;


abstract class Content
{
    private $content = null;
    private $contentType = null;
   
    public function __construct($content, $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return null
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    public abstract function headers();
    
    public function __toString()
    {
        $tempBody  = '';
        $tempBody .= implode("\n", $this->headers());
        $tempBody .= "\n\n";
        $tempBody .= $this->content . "\n";
        return $tempBody;
    }
}