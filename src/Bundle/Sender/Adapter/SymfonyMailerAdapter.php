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

namespace Sylius\Bundle\MailerBundle\Sender\Adapter;

use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class SymfonyMailerAdapter extends AbstractAdapter implements CcAwareAdapterInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
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
        $this->sendMessage(
            $renderedEmail,
            $senderAddress,
            $senderName,
            $recipients,
            $replyTo,
            $attachments,
            $email,
            $data,
        );
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
        $this->sendMessage(
            $renderedEmail,
            $senderAddress,
            $senderName,
            $recipients,
            $replyTo,
            $attachments,
            $email,
            $data,
            $ccRecipients,
            $bccRecipients,
        );
    }

    private function sendMessage(
        RenderedEmail $renderedEmail,
        string $senderAddress,
        string $senderName,
        array $recipients,
        array $replyTo,
        array $attachments,
        EmailInterface $email,
        array $data,
        array $ccRecipients = [],
        array $bccRecipients = [],
    ): void {
        $message = (new Email())
            ->subject($renderedEmail->getSubject())
            ->from(new Address($senderAddress, $senderName))
            ->to(...$recipients)
            ->replyTo(...$replyTo)
            ->html($renderedEmail->getBody())
        ;

        $message->addCc(...$ccRecipients);
        $message->addBcc(...$bccRecipients);

        foreach ($attachments as $attachment) {
            $message->attachFromPath($attachment);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        $this->dispatcher?->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

        $this->mailer->send($message);

        $this->dispatcher?->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
    }
}
