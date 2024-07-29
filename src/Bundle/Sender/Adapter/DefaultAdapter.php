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

namespace Sylius\Bundle\MailerBundle\Sender\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DefaultAdapter extends AbstractAdapter
{
    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
    ): void {
        throw new \RuntimeException(sprintf(
            'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
            SymfonyMailerAdapter::class,
        ));
    }

    public function sendWithCC(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
        array $ccRecipients = [],
        array $bccRecipients = [],
    ): void {
        throw new \RuntimeException(sprintf(
            'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
            SymfonyMailerAdapter::class,
        ));
    }
}
