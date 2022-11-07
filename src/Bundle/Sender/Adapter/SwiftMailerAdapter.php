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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @deprecated The Swift Mailer integration is deprecated since sylius/mailer-bundle 1.8. Use the Symfony Mailer integration instead.
 */
class SwiftMailerAdapter extends AbstractAdapter implements CcAwareAdapterInterface
{
    /** @var \Swift_Mailer */
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer, ?EventDispatcherInterface $dispatcher = null)
    {
        trigger_deprecation(
            'sylius/mailer-bundle',
            '1.8',
            'The Swift Mailer integration is deprecated and will be removed in 2.0. Use the Symfony Mailer integration instead.',
        );

        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
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
        $message = (new \Swift_Message())
            ->setSubject($renderedEmail->getSubject())
            ->setFrom([$senderAddress => $senderName])
            ->setTo($recipients)
            ->setReplyTo($replyTo)
        ;

        if (!empty($ccRecipients)) {
            $message->setCc($ccRecipients);
        }
        if (!empty($bccRecipients)) {
            $message->setBcc($bccRecipients);
        }

        $message->setBody($renderedEmail->getBody(), 'text/html');

        foreach ($attachments as $attachment) {
            $file = \Swift_Attachment::fromPath($attachment);

            $message->attach($file);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        if ($this->dispatcher !== null) {
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);
        }

        $this->mailer->send($message);

        if ($this->dispatcher !== null) {
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
        }
    }
}
