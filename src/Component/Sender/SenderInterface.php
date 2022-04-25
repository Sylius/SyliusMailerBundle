<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Mailer\Sender;

interface SenderInterface
{
    /**
     * @param string[] $recipients A list of email addresses to receive the message.
     * @param string[] $attachments A list of file paths to attach to the message.
     * @param string[] $replyTo A list of email addresses to set as the Reply-To address for the message.
     */
    public function send(string $code, array $recipients, array $data = [], array $attachments = [], array $replyTo = []): void;
}