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

namespace Sylius\Bundle\MailerBundle\test\src\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\MailerBundle\DependencyInjection\SyliusMailerExtension;

final class SyliusMailerExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_configures_the_bundle_with_the_default_configuration(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'Example.com Store');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'no-reply@example.com');
        $this->assertContainerBuilderHasParameter('sylius.mailer.emails');
        $this->assertContainerBuilderHasParameter('sylius.mailer.templates');
    }

    /** @test */
    public function it_configures_the_bundle_with_custom_sender_data(): void
    {
        $this->load(['sender' => ['name' => 'John Doe', 'address' => 'john@doe.com']]);

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'John Doe');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'john@doe.com');
    }

    /** @test */
    public function it_configures_the_bundle_with_custom_adapter_services(): void
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
