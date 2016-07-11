<?php
namespace Behoma\Mail;


class AttachmentFile extends Content
{
    private $name = null;
    public function __construct($content, $contentType, $name)
    {
        $this->name = $name ?: uniqid();
        parent::__construct(chunk_split(base64_encode($content)), $contentType);
    }

    public function headers()
    {
        return array(
            sprintf("Content-Type: %s; name=\"%s\"", $this->getContentType(), $this->name),
            sprintf("Content-Disposition: attachment; filename=\"%s\"", $this->name),
            "Content-Transfer-Encoding: base64",
        );
    }
}