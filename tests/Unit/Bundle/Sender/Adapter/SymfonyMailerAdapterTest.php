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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Bundle\Sender\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SymfonyMailerAdapterTest extends TestCase
{
    private MailerInterface&MockObject $mailer;

    private EventDispatcherInterface&MockObject $dispatcher;

    private SymfonyMailerAdapter $adapter;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->adapter = new SymfonyMailerAdapter($this->mailer);
    }

    #[Test]
    public function it_is_an_adapter(): void
    {
        $this->assertInstanceOf(AbstractAdapter::class, $this->adapter);
    }

    #[Test]
    public function it_sends_an_email_with_events(): void
    {
        $this->adapter->setEventDispatcher($this->dispatcher);

        $renderedEmail = new RenderedEmail('subject', 'body');
        $email = new Email();

        $this->dispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (EmailSendEvent $event, string $eventName) {
                $this->assertContains($eventName, [
                    SyliusMailerEvents::EMAIL_PRE_SEND,
                    SyliusMailerEvents::EMAIL_POST_SEND,
                ]);

                return $event;
            });

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (MimeEmail $message): bool {
                return $message->getSubject() === 'subject' &&
                    $message->getBody()->bodyToString() === 'body' &&
                    $message->getFrom()[0] == new Address('arnaud@sylius.com', 'arnaud') &&
                    $message->getTo()[0] == new Address('pawel@sylius.com');
            }));

        $this->adapter->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            [],
        );
    }

    #[Test]
    public function it_sends_an_email_with_cc_and_bcc(): void
    {
        $renderedEmail = new RenderedEmail('subject', 'body');
        $email = new Email();

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (MimeEmail $message): bool {
                return $message->getSubject() === 'subject' &&
                    $message->getBody()->bodyToString() === 'body' &&
                    $message->getFrom()[0] == new Address('arnaud@sylius.com', 'arnaud') &&
                    $message->getTo()[0] == new Address('pawel@sylius.com') &&
                    $message->getCc()[0] == new Address('cc@example.com') &&
                    $message->getBcc()[0] == new Address('bcc@example.com');
            }));

        $this->adapter->sendWithCC(
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

    #[Test]
    public function it_sends_an_email_with_attachments(): void
    {
        $renderedEmail = new RenderedEmail('subject', 'body');
        $email = new Email();

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(MimeEmail::class));

        $this->adapter->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            [__FILE__],
        );
    }

    #[Test]
    public function it_does_not_handle_exceptions_from_the_mailer(): void
    {
        $renderedEmail = new RenderedEmail('subject', 'body');
        $email = new Email();
        $exception = new TransportException('Testing');

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willThrowException($exception);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Testing');

        $this->adapter->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            $renderedEmail,
            $email,
            [],
        );
    }
}
