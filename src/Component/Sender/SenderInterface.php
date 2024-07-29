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

namespace Sylius\Component\Mailer\Sender;

interface SenderInterface
{
    /**
     * @deprecated using this method without 2 last arguments ($ccRecipients and $bccRecipients) is deprecated since 1.8 and won't be possible since 2.0
     *
     * @param string[]|null[] $recipients A list of email addresses to receive the message. Providing null or empty string in the list of recipients is deprecated and will be removed in SyliusMailerBundle 2.0
     * @param string[] $attachments A list of file paths to attach to the message.
     * @param string[] $replyTo A list of email addresses to set as the Reply-To address for the message.
     * @param string[] $ccRecipients A list of email addresses set as carbon copy
     * @param string[] $bccRecipients A list of email addresses set as blind carbon copy
     */
    public function send(
        string $code,
        array $recipients,
        array $data = [],
        array $attachments = [],
        array $replyTo = [],
    ): void;
}
