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

namespace spec\Sylius\Component\Mailer\Sender;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface as SenderAdapterInterface;

final class SenderSpec extends ObjectBehavior
{
    function let(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        EmailProviderInterface $provider,
        DefaultSettingsProviderInterface $defaultSettingsProvider,
    ): void {
        $this->beConstructedWith($rendererAdapter, $senderAdapter, $provider, $defaultSettingsProvider);
    }

    function it_sends_an_email_through_the_adapter(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RenderedEmail $renderedEmail,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $provider->getEmail('bar')->willReturn($email);
        $email->isEnabled()->willReturn(true);
        $email->getSenderAddress()->willReturn('sender@example.com');
        $email->getSenderName()->willReturn('Sender');

        $data = ['foo' => 2];

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->send(
            ['john@example.com'],
            'sender@example.com',
            'Sender',
            $renderedEmail,
            $email,
            $data,
            [],
            [],
        )->shouldBeCalled();

        $this->send('bar', ['john@example.com'], $data, [], []);
    }

    function it_sends_an_email_and_name_pair_through_the_adapter(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RenderedEmail $renderedEmail,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $provider->getEmail('bar')->willReturn($email);
        $email->isEnabled()->willReturn(true);
        $email->getSenderAddress()->willReturn('sender@example.com');
        $email->getSenderName()->willReturn('Sender');

        $data = ['foo' => 2];

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->send(
            ['john@example.com' => 'John Doe'],
            'sender@example.com',
            'Sender',
            $renderedEmail,
            $email,
            $data,
            [],
            [],
        )->shouldBeCalled();

        $this->send('bar', ['john@example.com' => 'John Doe'], $data, [], []);
    }

    function it_sends_an_email_with_cc_and_bcc_through_the_adapter(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RenderedEmail $renderedEmail,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $provider->getEmail('bar')->willReturn($email);
        $email->isEnabled()->willReturn(true);
        $email->getSenderAddress()->willReturn('sender@example.com');
        $email->getSenderName()->willReturn('Sender');

        $data = ['foo' => 2];

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->sendWithCC(
            ['john@example.com'],
            'sender@example.com',
            'Sender',
            $renderedEmail,
            $email,
            $data,
            [],
            [],
            ['cc@example.com'],
            ['bcc@example.com'],
        )->shouldBeCalled();

        $this->send('bar', ['john@example.com'], $data, [], [], ['cc@example.com'], ['bcc@example.com']);
    }

    function it_sends_an_email_with_cc_and_name_pair_and_bcc_and_name_pair_through_the_adapter(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RenderedEmail $renderedEmail,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $provider->getEmail('bar')->willReturn($email);
        $email->isEnabled()->willReturn(true);
        $email->getSenderAddress()->willReturn('sender@example.com');
        $email->getSenderName()->willReturn('Sender');

        $data = ['foo' => 2];

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->sendWithCC(
            ['john@example.com'],
            'sender@example.com',
            'Sender',
            $renderedEmail,
            $email,
            $data,
            [],
            [],
            ['cc@example.com' => 'CC'],
            ['bcc@example.com' => 'BCC'],
        )->shouldBeCalled();

        $this->send('bar', ['john@example.com'], $data, [], [], ['cc@example.com' => 'CC'], ['bcc@example.com' => 'BCC']);
    }

    function it_sends_a_modified_email_with_cc_and_bcc_through_the_adapter(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RenderedEmail $renderedEmail,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        DefaultSettingsProviderInterface $defaultSettingsProvider,
        EmailModifierInterface $emailModifier,
    ): void {
        $this->beConstructedWith($rendererAdapter, $senderAdapter, $provider, $defaultSettingsProvider, $emailModifier);

        $provider->getEmail('bar')->willReturn($email);

        $data = ['foo' => 2];

        $emailModifier->modify($email, $data)->shouldBeCalled()->willReturn($email);

        $email->isEnabled()->willReturn(true);
        $email->getSenderAddress()->willReturn('sender@example.com');
        $email->getSenderName()->willReturn('Modified sender');

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->sendWithCC(
            ['john@example.com'],
            'sender@example.com',
            'Modified sender',
            $renderedEmail,
            $email,
            $data,
            [],
            [],
            ['cc@example.com'],
            ['bcc@example.com'],
        )->shouldBeCalled();

        $this->send('bar', ['john@example.com'], $data, [], [], ['cc@example.com'], ['bcc@example.com']);
    }

    function it_does_not_send_disabled_emails(
        EmailInterface $email,
        EmailProviderInterface $provider,
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $provider->getEmail('bar')->willReturn($email);
        $email->isEnabled()->willReturn(false);

        $rendererAdapter->render($email, ['foo' => 2])->shouldNotBeCalled();
        $senderAdapter->send(['john@example.com'], 'mail@sylius.com', 'Sylius Mailer', null, $email, [], [], [])->shouldNotBeCalled();

        $this->send('bar', ['john@example.com'], ['foo' => 2], []);
    }

    function it_throws_an_exception_if_wrong_value_is_provided_as_recipient_email(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
    ): void {
        $rendererAdapter->render(Argument::any())->shouldNotBeCalled();
        $senderAdapter->send(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during(
            'send',
            ['bar', ['john@example.com', null], ['foo' => 2], []],
        );

        $this->shouldThrow(\InvalidArgumentException::class)->during(
            'send',
            ['bar', [5], ['foo' => 2], []],
        );

        $this->shouldThrow(\InvalidArgumentException::class)->during(
            'send',
            ['bar', [''], ['foo' => 2], []],
        );
    }
}
