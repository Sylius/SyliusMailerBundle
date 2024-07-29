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

namespace spec\Sylius\Bundle\MailerBundle\Renderer\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

final class EmailTwigAdapterSpec extends ObjectBehavior
{
    function let(Environment $twig, EventDispatcherInterface $dispatcher): void
    {
        $this->beConstructedWith($twig, $dispatcher);
    }

    function it_is_an_adapter(): void
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_renders_an_email(
        Environment $twig,
        Template $template,
        EmailInterface $email,
        EmailRenderEvent $event,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail,
    ): void {
        $twig->mergeGlobals([])->shouldBeCalled()->willReturn([]);

        $email->getTemplate()->shouldBeCalled()->willReturn('MyTemplate');
        $twig->load('MyTemplate')->willReturn(new TemplateWrapper($twig->getWrappedObject(), $template->getWrappedObject()));

        $template->renderBlock('subject', [])->willReturn('template');
        $template->renderBlock('body', [])->willReturn('body');

        $dispatcher
            ->dispatch(Argument::type(EmailRenderEvent::class), SyliusMailerEvents::EMAIL_PRE_RENDER)
            ->shouldBeCalled()
            ->willReturn($event)
        ;

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, [])->shouldReturn($renderedEmail);
    }

    function it_creates_and_renders_an_email(
        EmailInterface $email,
        EmailRenderEvent $event,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail,
    ): void {
        $email->getTemplate()->shouldBeCalled()->willReturn(null);
        $email->getSubject()->shouldBeCalled()->willReturn('subject');
        $email->getContent()->shouldBeCalled()->willReturn('content');

        $dispatcher
            ->dispatch(Argument::type(EmailRenderEvent::class), SyliusMailerEvents::EMAIL_PRE_RENDER)
            ->shouldBeCalled()
            ->willReturn($event)
        ;

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, [])->shouldReturn($renderedEmail);
    }
}
