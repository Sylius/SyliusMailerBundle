<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\tests\Model;

final class SentMessage
{
    private string $subject;
    private string $body;

    public static function fromSwiftMessage(\Swift_Message $message): self
    {
        return new self($message->getSubject(), $message->getBody());
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    private function __construct(string $subject, string $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }
}
