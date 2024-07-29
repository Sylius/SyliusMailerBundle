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

use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class RendererAdapterPassTest extends KernelTestCase
{
    /** @test */
    public function it_has_swiftmailer_adapter_configured_by_default(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $senderAdapter = $container->get('sylius.email_renderer.adapter');

        $this->assertInstanceOf(EmailTwigAdapter::class, $senderAdapter);

        $this->assertNotNull(
            $container->get('sylius.email_renderer.adapter.twig', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        );
    }
}
