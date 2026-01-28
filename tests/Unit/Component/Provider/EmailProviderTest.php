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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Component\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Provider\EmailProvider;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

final class EmailProviderTest extends TestCase
{
    private EmailFactoryInterface&MockObject $emailFactory;

    private EmailProvider $provider;

    protected function setUp(): void
    {
        $this->emailFactory = $this->createMock(EmailFactoryInterface::class);

        $emails = [
            'user_confirmation' => [
                'enabled' => false,
                'subject' => 'Hello test!',
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'John Doe',
                    'address' => 'john@doe.com',
                ],
            ],
            'order_cancelled' => [
                'enabled' => false,
                'subject' => 'Hi test!',
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'Rick Doe',
                    'address' => 'john@doe.com',
                ],
            ],
        ];

        $this->provider = new EmailProvider($this->emailFactory, $emails);
    }

    public function testImplementsEmailProviderInterface(): void
    {
        $this->assertInstanceOf(EmailProviderInterface::class, $this->provider);
    }

    public function testLooksForEmailInConfigurationWhenItCannotBeFoundViaRepository(): void
    {
        $email = new Email();

        $this->emailFactory
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($email);

        $result = $this->provider->getEmail('user_confirmation');

        $this->assertSame($email, $result);
        $this->assertSame('user_confirmation', $email->getCode());
        $this->assertSame('Hello test!', $email->getSubject());
        $this->assertSame('@SyliusMailer/default.html.twig', $email->getTemplate());
        $this->assertSame('John Doe', $email->getSenderName());
        $this->assertSame('john@doe.com', $email->getSenderAddress());
        $this->assertFalse($email->isEnabled());
    }
}
