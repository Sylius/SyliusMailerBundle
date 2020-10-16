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

use Sylius\Bundle\MailerBundle\Exception\SyliusMailerException;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SymfonyMailerAdapter extends AbstractAdapter
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer, ?EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($dispatcher);

        $this->mailer = $mailer;
    }

    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = []
    ): void {
        $message = (new Email())
            ->subject($renderedEmail->getSubject())
            ->from(new Address($senderAddress, $senderName))
            ->html($renderedEmail->getBody())
        ;

        foreach ($this->convertToAddresses($recipients) as $recipient) {
            $message->addTo($recipient);
        }

        foreach ($attachments as $attachment) {
            $message->attachFromPath($attachment);
        }

        foreach ($this->convertToAddresses($replyTo) as $replyToAddress) {
            $message->addReplyTo($replyToAddress);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        if ($this->dispatcher !== null) {
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);
        }

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $transportException) {
            throw new SyliusMailerException(
                'Failed to send email message: ' . $transportException->getMessage(),
                0,
                $transportException
            );
        }

        if ($this->dispatcher !== null) {
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
        }
    }

    private function convertToAddresses(array $addresses): \Generator
    {
        foreach ($addresses as $key => $value) {
            if (is_string($key)) {
                yield new Address($key, $value);
            } else {
                yield new Address($value);
            }
        }
    }
}
