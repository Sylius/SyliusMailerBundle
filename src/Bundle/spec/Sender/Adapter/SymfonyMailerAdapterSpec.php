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

namespace spec\Sylius\Bundle\MailerBundle\Sender\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
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

    function it_sends_an_email(
        MailerInterface $mailer,
        EmailInterface $email,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail
    ): void {
        $this->setEventDispatcher($dispatcher);

        $renderedEmail->getSubject()->shouldBeCalled()->willReturn('subject');
        $renderedEmail->getBody()->shouldBeCalled()->willReturn('body');

        $dispatcher
            ->dispatch(Argument::type(EmailSendEvent::class), SyliusMailerEvents::EMAIL_PRE_SEND)
            ->shouldBeCalled()
        ;

        $mailer->send(Argument::type(RawMessage::class))->shouldBeCalled();

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
            []
        );
    }
}
