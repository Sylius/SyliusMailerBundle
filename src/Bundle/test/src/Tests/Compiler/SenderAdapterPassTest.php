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

namespace App\Tests\Compiler;

use Sylius\Bundle\MailerBundle\Sender\Adapter\DefaultAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SenderAdapterPassTest extends KernelTestCase
{
    /** @test */
    public function it_has_symfony_mailer_adapter_configured_by_default(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_sender.adapter');

        $this->assertInstanceOf(SymfonyMailerAdapter::class, $senderAdapter);

        $this->assertNotNull(
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
            $container->get('sylius.email_sender.adapter.symfony_mailer', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }
}
