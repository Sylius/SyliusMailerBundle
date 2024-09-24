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

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Webmozart\Assert\Assert;

final class SymfonyMailerAdapter extends AbstractAdapter implements CcAwareAdapterInterface
{
    public function __construct(private MailerInterface $mailer)
    {
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
        Assert::allStringNotEmpty($recipients);
        Assert::allStringNotEmpty($replyTo);

        $message = (new Email())
            ->subject($renderedEmail->getSubject())
            ->from(new Address($senderAddress, $senderName))
            ->to(...$this->formatRecipients($recipients))
            ->replyTo(...$replyTo)
            ->html($renderedEmail->getBody())
        ;

        $message->addCc(...$this->formatRecipients($ccRecipients));
        $message->addBcc(...$this->formatRecipients($bccRecipients));

        foreach ($attachments as $attachment) {
            $message->attachFromPath($attachment);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        $this->dispatcher?->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

        $this->mailer->send($message);

        $this->dispatcher?->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
    }

    /**
     * There are two kinds of recipient array syntax that can be passed to the send method:
     * - Only email addresses: ['john.doe@mail.com', 'jane.smith@mail.com']
     * - Email addresses with names: ['john.doe@mail.com' => 'John Doe', 'jane.smith@mail.com' => 'Jane Smith']
     *
     * Since Symfony\Mailer requires an instance of Symfony\Component\Mime\Address or a valid email string for each recipient,
     * we need to transform the recipient array where keys are address and values are name to an array of Address object.
     */
    protected function formatRecipients(array $recipients): array
    {
        $transformedRecipients = [];
        $validator = new EmailValidator();
        foreach ($recipients as $addressOrKey => $nameOrAddress) {
            if (\is_string($addressOrKey) && $validator->isValid($addressOrKey, new RFCValidation())) {
                $transformedRecipients[] = new Address($addressOrKey, $nameOrAddress);

                continue;
            }

            $transformedRecipients[] = $nameOrAddress;
        }

        return $transformedRecipients;
    }
}
