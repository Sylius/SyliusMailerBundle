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

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\MailerBundle\Sender\Adapter\DefaultAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;

final class DefaultAdapterTest extends TestCase
{
    private DefaultAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new DefaultAdapter();
    }

    public function testIsAnAdapter(): void
    {
        $this->assertInstanceOf(AbstractAdapter::class, $this->adapter);
    }

    public function testThrowsExceptionAboutNotConfiguredEmailSenderAdapterForSend(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
            SymfonyMailerAdapter::class,
        ));

        $this->adapter->send(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            new RenderedEmail('subject', 'body'),
            new Email(),
            [],
        );
    }

    public function testThrowsExceptionAboutNotConfiguredEmailSenderAdapterForSendWithCc(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
            SymfonyMailerAdapter::class,
        ));

        $this->adapter->sendWithCC(
            ['pawel@sylius.com'],
            'arnaud@sylius.com',
            'arnaud',
            new RenderedEmail('subject', 'body'),
            new Email(),
            [],
            [],
            [],
            ['cc@example.com'],
            ['bcc@example.com'],
        );
    }
}
