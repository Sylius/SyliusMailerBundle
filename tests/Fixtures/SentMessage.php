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

namespace Sylius\Bundle\MailerBundle\Tests\Fixtures;

use Symfony\Component\Mime\Email;

final class SentMessage
{
    public function __construct(
        private readonly string $subject,
        private readonly string $body,
    ) {
    }

    public static function fromEmail(Email $email): self
    {
        return new self($email->getSubject(), $email->getBody()->toString());
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
