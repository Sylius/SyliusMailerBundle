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

namespace App\Tests\Compiler;

use Sylius\Bundle\MailerBundle\Sender\Adapter\DefaultAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SenderAdapterPassTest extends KernelTestCase
{
    /** @test */
    public function it_has_swiftmailer_adapter_configured_by_default(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_sender.adapter');

        $this->assertInstanceOf(SwiftMailerAdapter::class, $senderAdapter);

        $this->assertNotNull(
            $container->get('sylius.email_sender.adapter.swiftmailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
        $this->assertNotNull(
            $container->get('sylius.email_sender.adapter.symfony_mailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }

    /** @test */
    public function it_can_have_mailer_adapter_configured(): void
    {
        self::bootKernel(['environment' => 'test_with_symfony_mailer']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_sender.adapter');

        $this->assertInstanceOf(SymfonyMailerAdapter::class, $senderAdapter);

        $this->assertNull(
            $container->get('sylius.email_sender.adapter.swiftmailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
        $this->assertNotNull(
            $container->get('sylius.email_sender.adapter.symfony_mailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }

    /** @test */
    public function it_can_have_swiftmailer_adapter_configured_explicitly(): void
    {
        self::bootKernel(['environment' => 'test_with_swiftmailer']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_sender.adapter');

        $this->assertInstanceOf(SwiftMailerAdapter::class, $senderAdapter);

        $this->assertNotNull(
            $container->get('sylius.email_sender.adapter.swiftmailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
        $this->assertNull(
            $container->get('sylius.email_sender.adapter.symfony_mailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }

    /** @test */
    public function it_does_not_fail_if_there_are_no_mailers_available(): void
    {
        self::bootKernel(['environment' => 'test_with_no_mailers']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_sender.adapter');

        $this->assertInstanceOf(DefaultAdapter::class, $senderAdapter);

        $this->assertNull(
            $container->get('sylius.email_sender.adapter.swiftmailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
        $this->assertNull(
            $container->get('sylius.email_sender.adapter.symfony_mailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }
}
