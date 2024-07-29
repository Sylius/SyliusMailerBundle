<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\tests\Model;

use Symfony\Component\Mime\Email as SymfonyEmail;

final class SentMessage
{
    private string $subject;

    private string $body;

    public static function fromSwiftMessage(\Swift_Message $message): self
    {
        return new self($message->getSubject(), $message->getBody());
    }

    public static function fromSymfonyMessage(SymfonyEmail $message): self
    {
        return new self($message->getSubject(), $message->getBody()->toString());
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
