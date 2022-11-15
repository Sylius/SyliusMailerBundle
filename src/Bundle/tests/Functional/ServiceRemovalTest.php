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

namespace Sylius\Bundle\MailerBundle\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ServiceRemovalTest extends KernelTestCase
{
    /**
     * @test
     *
     * @group symfony-mailer
     */
    public function it_removes_swiftmailer_adapter_when_its_not_present(): void
    {
        $kernel = self::bootKernel(['environment' => 'test_with_symfony_mailer']);
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('sylius.email_sender.adapter.symfony_mailer'));
        $this->assertFalse($container->has('sylius.email_sender.adapter.swiftmailer'));
    }

    /**
     * @test
     *
     * @group swiftmailer
     */
    public function it_removes_symfony_mailer_adapter_when_its_not_present(): void
    {
        $kernel = self::bootKernel(['environment' => 'test_with_swiftmailer']);
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('sylius.email_sender.adapter.swiftmailer'));
        $this->assertFalse($container->has('sylius.email_sender.adapter.symfony_mailer'));
    }
}
