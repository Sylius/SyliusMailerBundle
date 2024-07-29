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

namespace Sylius\Component\Mailer\Event;

use Sylius\Component\Mailer\Model\EmailInterface;
use SyliusLabs\Polyfill\Symfony\EventDispatcher\Event;

final class EmailSendEvent extends Event
{
    /**
     * @param mixed $message
     * @param string[] $recipients
     * @param string[] $replyTo
     */
    public function __construct(
        protected $message,
        protected EmailInterface $email,
        protected array $data,
        protected array $recipients = [],
        protected array $replyTo = [],
    ) {
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getEmail(): EmailInterface
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string[]
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }
}
