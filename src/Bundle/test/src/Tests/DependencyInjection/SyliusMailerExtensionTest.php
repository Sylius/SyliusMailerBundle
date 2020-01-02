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

namespace Sylius\Bundle\MailerBundle\test\src\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\MailerBundle\DependencyInjection\SyliusMailerExtension;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailDefaultAdapter;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\DefaultAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Environment;

final class SyliusMailerExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_configures_mailer_adapters_and_sender_with_default_data(): void
    {
        $this->mockService('event_dispatcher', EventDispatcher::class);

        $this->container->setParameter('kernel.bundles', []);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'Example.com Store');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'no-reply@example.com');

        $this->assertContainerBuilderHasService('sylius.email_sender.adapter.default', DefaultAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.default');

        $this->assertContainerBuilderHasService('sylius.email_renderer.adapter.default', EmailDefaultAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.default');
    }

    /** @test */
    public function it_configures_mailer_adapters_and_sender_with_custom_sender_data(): void
    {
        $this->mockService('event_dispatcher', EventDispatcher::class);
        $this->container->setParameter('kernel.bundles', []);

        $this->load(['sender' => ['name' => 'John Doe', 'address' => 'john@doe.com']]);
        $this->compile();

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'John Doe');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'john@doe.com');

        $this->assertContainerBuilderHasService('sylius.email_sender.adapter.default', DefaultAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.default');

        $this->assertContainerBuilderHasService('sylius.email_renderer.adapter.default', EmailDefaultAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.default');
    }

    /** @test */
    public function it_configures_twig_and_swiftmailer_adapters_if_they_are_available(): void
    {
        $this->mockService('event_dispatcher', EventDispatcher::class);
        $this->mockService('mailer', \Swift_Mailer::class);
        $this->mockService('twig', Environment::class);

        $this->container->setParameter(
            'kernel.bundles',
            ['SwiftmailerBundle' => SwiftmailerBundle::class, 'TwigBundle' => TwigBundle::class]
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_name', 'Example.com Store');
        $this->assertContainerBuilderHasParameter('sylius.mailer.sender_address', 'no-reply@example.com');

        $this->assertContainerBuilderHasService('sylius.email_sender.adapter.swiftmailer', SwiftMailerAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.swiftmailer');

        $this->assertContainerBuilderHasService('sylius.email_renderer.adapter.twig', EmailTwigAdapter::class);
        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.twig');
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusMailerExtension()];
    }

    private function mockService(string $id, string $class): void
    {
        $this->container->setDefinition(
            $id, (new Definition())->setClass(self::getMockClass($class))->setAutoconfigured(true)
        );
    }
}
