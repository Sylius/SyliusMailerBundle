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

namespace Sylius\Bundle\MailerBundle\Tests\Functional\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\MailerBundle\DependencyInjection\SyliusMailerExtension;

final class SyliusMailerExtensionTest extends AbstractExtensionTestCase
{
    public function testConfiguresWithDefaultConfiguration(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'Example.com Store');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'no-reply@example.com');
        $this->assertContainerBuilderHasParameter('sylius.mailer.emails');
        $this->assertContainerBuilderHasParameter('sylius.mailer.templates');
    }

    public function testConfiguresWithCustomSenderData(): void
    {
        $this->load(['sender' => ['name' => 'John Doe', 'address' => 'john@doe.com']]);

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'John Doe');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'john@doe.com');
    }

    public function testConfiguresWithCustomAdapterServices(): void
    {
        $this->load(['sender_adapter' => 'sylius.email_sender.adapter.custom', 'renderer_adapter' => 'sylius.email_renderer.adapter.custom']);

        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.custom');
        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.custom');
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusMailerExtension()];
    }
}
