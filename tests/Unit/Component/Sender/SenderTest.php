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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Component\Sender;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface as SenderAdapterInterface;
use Sylius\Component\Mailer\Sender\Sender;

final class SenderTest extends TestCase
{
    private RendererAdapterInterface&MockObject $rendererAdapter;

    private SenderAdapterInterface&MockObject $senderAdapter;

    private EmailProviderInterface&MockObject $provider;

    private DefaultSettingsProviderInterface&MockObject $defaultSettingsProvider;

    private Sender $sender;

    protected function setUp(): void
    {
        $this->rendererAdapter = $this->createMock(RendererAdapterInterface::class);
        $this->senderAdapter = $this->createMock(SenderAdapterInterface::class);
        $this->provider = $this->createMock(EmailProviderInterface::class);
        $this->defaultSettingsProvider = $this->createMock(DefaultSettingsProviderInterface::class);

        $this->sender = new Sender(
            $this->rendererAdapter,
            $this->senderAdapter,
            $this->provider,
            $this->defaultSettingsProvider,
        );
    }

    #[Test]
    public function it_sends_an_email_through_the_adapter(): void
    {
        $email = new Email();
        $email->setEnabled(true);
        $email->setSenderAddress('sender@example.com');
        $email->setSenderName('Sender');

        $renderedEmail = new RenderedEmail('Subject', 'Body');

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->once())
            ->method('render')
            ->with($email, ['foo' => 2])
            ->willReturn($renderedEmail);

        $this->senderAdapter
            ->expects($this->once())
            ->method('send')
            ->with(
                ['john@example.com'],
                'sender@example.com',
                'Sender',
                $renderedEmail,
                $email,
                ['foo' => 2],
                [],
                [],
            );

        $this->sender->send('bar', ['john@example.com'], ['foo' => 2], [], []);
    }

    #[Test]
    public function it_sends_an_email_and_name_pair_through_the_adapter(): void
    {
        $email = new Email();
        $email->setEnabled(true);
        $email->setSenderAddress('sender@example.com');
        $email->setSenderName('Sender');

        $renderedEmail = new RenderedEmail('Subject', 'Body');

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->once())
            ->method('render')
            ->with($email, ['foo' => 2])
            ->willReturn($renderedEmail);

        $this->senderAdapter
            ->expects($this->once())
            ->method('send')
            ->with(
                ['john@example.com' => 'John Doe'],
                'sender@example.com',
                'Sender',
                $renderedEmail,
                $email,
                ['foo' => 2],
                [],
                [],
            );

        $this->sender->send('bar', ['john@example.com' => 'John Doe'], ['foo' => 2], [], []);
    }

    #[Test]
    public function it_sends_an_email_with_cc_and_bcc_through_the_adapter(): void
    {
        $email = new Email();
        $email->setEnabled(true);
        $email->setSenderAddress('sender@example.com');
        $email->setSenderName('Sender');

        $renderedEmail = new RenderedEmail('Subject', 'Body');

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->once())
            ->method('render')
            ->with($email, ['foo' => 2])
            ->willReturn($renderedEmail);

        $this->senderAdapter
            ->expects($this->once())
            ->method('sendWithCC')
            ->with(
                ['john@example.com'],
                'sender@example.com',
                'Sender',
                $renderedEmail,
                $email,
                ['foo' => 2],
                [],
                [],
                ['cc@example.com'],
                ['bcc@example.com'],
            );

        $this->sender->send('bar', ['john@example.com'], ['foo' => 2], [], [], ['cc@example.com'], ['bcc@example.com']);
    }

    #[Test]
    public function it_sends_an_email_with_cc_and_name_pair_and_bcc_and_name_pair_through_the_adapter(): void
    {
        $email = new Email();
        $email->setEnabled(true);
        $email->setSenderAddress('sender@example.com');
        $email->setSenderName('Sender');

        $renderedEmail = new RenderedEmail('Subject', 'Body');

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->once())
            ->method('render')
            ->with($email, ['foo' => 2])
            ->willReturn($renderedEmail);

        $this->senderAdapter
            ->expects($this->once())
            ->method('sendWithCC')
            ->with(
                ['john@example.com'],
                'sender@example.com',
                'Sender',
                $renderedEmail,
                $email,
                ['foo' => 2],
                [],
                [],
                ['cc@example.com' => 'CC'],
                ['bcc@example.com' => 'BCC'],
            );

        $this->sender->send('bar', ['john@example.com'], ['foo' => 2], [], [], ['cc@example.com' => 'CC'], ['bcc@example.com' => 'BCC']);
    }

    #[Test]
    public function it_sends_a_modified_email_with_cc_and_bcc_through_the_adapter(): void
    {
        /** @var EmailModifierInterface&MockObject $emailModifier */
        $emailModifier = $this->createMock(EmailModifierInterface::class);

        $sender = new Sender(
            $this->rendererAdapter,
            $this->senderAdapter,
            $this->provider,
            $this->defaultSettingsProvider,
            $emailModifier,
        );

        $email = new Email();
        $email->setEnabled(true);
        $email->setSenderAddress('sender@example.com');
        $email->setSenderName('Modified sender');

        $renderedEmail = new RenderedEmail('Subject', 'Body');

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $emailModifier
            ->expects($this->once())
            ->method('modify')
            ->with($email, ['foo' => 2])
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->once())
            ->method('render')
            ->with($email, ['foo' => 2])
            ->willReturn($renderedEmail);

        $this->senderAdapter
            ->expects($this->once())
            ->method('sendWithCC')
            ->with(
                ['john@example.com'],
                'sender@example.com',
                'Modified sender',
                $renderedEmail,
                $email,
                ['foo' => 2],
                [],
                [],
                ['cc@example.com'],
                ['bcc@example.com'],
            );

        $sender->send('bar', ['john@example.com'], ['foo' => 2], [], [], ['cc@example.com'], ['bcc@example.com']);
    }

    #[Test]
    public function it_does_not_send_disabled_emails(): void
    {
        $email = new Email();
        $email->setEnabled(false);

        $this->provider
            ->expects($this->once())
            ->method('getEmail')
            ->with('bar')
            ->willReturn($email);

        $this->rendererAdapter
            ->expects($this->never())
            ->method('render');

        $this->senderAdapter
            ->expects($this->never())
            ->method('send');

        $this->sender->send('bar', ['john@example.com'], ['foo' => 2], []);
    }

    #[Test]
    public function it_throws_an_exception_if_wrong_value_is_provided_as_recipient_email(): void
    {
        $this->rendererAdapter
            ->expects($this->never())
            ->method('render');

        $this->senderAdapter
            ->expects($this->never())
            ->method('send');

        $this->expectException(\InvalidArgumentException::class);

        $this->sender->send('bar', ['john@example.com', null], ['foo' => 2], []);
    }

    #[Test]
    public function it_throws_an_exception_if_integer_is_provided_as_recipient_email(): void
    {
        $this->rendererAdapter
            ->expects($this->never())
            ->method('render');

        $this->senderAdapter
            ->expects($this->never())
            ->method('send');

        $this->expectException(\InvalidArgumentException::class);

        $this->sender->send('bar', [5], ['foo' => 2], []);
    }

    #[Test]
    public function it_throws_an_exception_if_empty_string_is_provided_as_recipient_email(): void
    {
        $this->rendererAdapter
            ->expects($this->never())
            ->method('render');

        $this->senderAdapter
            ->expects($this->never())
            ->method('send');

        $this->expectException(\InvalidArgumentException::class);

        $this->sender->send('bar', [''], ['foo' => 2], []);
    }
}
