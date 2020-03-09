<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 14/8/2018
 * Time: 11:19 AM
 */

namespace application\model;


class MailContent
{
    private $fromMail;
    private $fromName;
    private $replyTo;
    private $replyToName;
    private $address=[];
    private $cc=[];
    private $bcc=[];
    private $html = true;
    private $subject;
    private $body;
    private $altBody;
    private $attachment=[];

    /**
     * @return mixed
     */
    public function getFromMail()
    {
        return $this->fromMail;
    }

    /**
     * @param mixed $fromMail
     */
    public function setFromMail($fromMail)
    {
        $this->fromMail = $fromMail;
    }

    /**
     * @return mixed
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param mixed $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return mixed
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param mixed $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    /**
     * @param array $address
     */
    public function setAddress(array $address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->html;
    }

    /**
     * @param bool $html
     */
    public function setHtml(bool $html)
    {
        $this->html = $html;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getAltBody()
    {
        return $this->altBody;
    }

    /**
     * @param mixed $altBody
     */
    public function setAltBody($altBody)
    {
        $this->altBody = $altBody;
    }

    /**
     * @return array
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    /**
     * @param array $attachment
     */
    public function setAttachment(array $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return mixed
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }

    /**
     * @param mixed $replyToName
     */
    public function setReplyToName($replyToName)
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return array
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @param array $cc
     */
    public function setCc(array $cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return array
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc)
    {
        $this->bcc = $bcc;
    }

}