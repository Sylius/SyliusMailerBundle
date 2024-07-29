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

namespace spec\Sylius\Bundle\MailerBundle\Sender\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SymfonyMailerAdapterSpec extends ObjectBehavior
{
    function let(MailerInterface $mailer): void
    {
        $this->beConstructedWith($mailer);
    }

    function it_is_an_adapter(): void
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_sends_an_email_with_events(
        MailerInterface $mailer,
        EmailInterface $email,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail,
    ): void {
        $this->setEventDispatcher($dispatcher);

        $renderedEmail->getSubject()->willReturn('subject');
        $renderedEmail->getBody()->willReturn('body');

        $dispatcher
            ->dispatch(Argument::type(EmailSendEvent::class), SyliusMailerEvents::EMAIL_PRE_SEND)
            ->shouldBeCalled()
        ;

        $mailer->send(Argument::that(function (Email $message): bool {
            return
                $message->getSubject() === 'subject' &&
                $message->getBody()->bodyToString() === 'body' &&
                $message->getFrom()[0] == new Address('arnaud@sylius.com', 'arnaud') &&
                $message->getTo()[0] == new Address('pawel@sylius.com')
            ;
        }))->shouldBeCalled();

        $dispatcher
            ->dispatch(Argument::type(EmailSendEvent::class), SyliusMailerEvents::EMAIL_POST_SEND)
            ->shouldBeCalled()
        ;

        $this->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            [],
        );
    }

    function it_sends_an_email_with_cc_and_bcc(
        MailerInterface $mailer,
        EmailInterface $email,
        RenderedEmail $renderedEmail,
    ): void {
        $renderedEmail->getSubject()->willReturn('subject');
        $renderedEmail->getBody()->willReturn('body');

        $mailer->send(Argument::that(function (Email $message): bool {
            return
                $message->getSubject() === 'subject' &&
                $message->getBody()->bodyToString() === 'body' &&
                $message->getFrom()[0] == new Address('arnaud@sylius.com', 'arnaud') &&
                $message->getTo()[0] == new Address('pawel@sylius.com') &&
                $message->getCc()[0] == new Address('cc@example.com') &&
                $message->getBcc()[0] == new Address('bcc@example.com')
            ;
        }))->shouldBeCalled();

        $this->sendWithCC(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            [],
            [],
            [],
            ['cc@example.com'],
            ['bcc@example.com'],
        );
    }

    function it_sends_an_email_with_attachments(
        MailerInterface $mailer,
        EmailInterface $email,
        RenderedEmail $renderedEmail,
    ): void {
        $renderedEmail->getSubject()->willReturn('subject');
        $renderedEmail->getBody()->willReturn('body');

        $mailer->send(Argument::type(Email::class))->shouldBeCalled();

        $this->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            ['/path/to/file1.txt', '/path/to/file2.txt'],
        );
    }

    function it_does_not_handle_exceptions_from_the_mailer(
        MailerInterface $mailer,
        EmailInterface $email,
        RenderedEmail $renderedEmail,
    ): void {
        $exception = new TransportException('Testing');

        $renderedEmail->getSubject()->willReturn('subject');
        $renderedEmail->getBody()->willReturn('body');

        $mailer->send(Argument::type(Email::class))->willThrow($exception);

        $this->shouldThrow($exception)->during(
            'send',
            [
                ['pawel@sylius.com'],
                'arnaud@sylius.com',
                'arnaud',
                $renderedEmail,
                $email,
                [],
            ],
        );
    }
}
