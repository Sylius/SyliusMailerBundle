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

namespace Sylius\Bundle\MailerBundle\DependencyInjection;

use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class SyliusMailerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $adapter = $this->configureSenderAdapter($container, $config);
        $container->setAlias('sylius.email_sender.adapter', $adapter);

        $this->configureRendererAdapter($container, $config);

        $container->setParameter('sylius.mailer.sender_name', $config['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $config['sender']['address']);

        $templates = $config['templates'] ?? ['Default' => '@SyliusMailer/default.html.twig'];

        $container->setParameter('sylius.mailer.emails', $config['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        $configuration = new Configuration();

        $container->addObjectResource($configuration);

        return $configuration;
    }

    private function configureSenderAdapter(ContainerBuilder $container, array $config): string
    {
        if (isset($config['sender_adapter'])) {
            return $config['sender_adapter'];
        }

        if ($container->get('service_container')->has('mailer.mailer')) {
            $symfonyMailerAdapter = new ChildDefinition('sylius.email_sender.adapter.abstract');
            $symfonyMailerAdapter->setClass(SymfonyMailerAdapter::class);
            $symfonyMailerAdapter->setArguments([new Reference('mailer.mailer'), new Reference('event_dispatcher')]);
            $symfonyMailerAdapter->setPrivate(false);

            $container->setDefinition('sylius.email_sender.adapter.symfony_mailer', $symfonyMailerAdapter);

            return 'sylius.email_sender.adapter.symfony_mailer';
        }

        if ($container->get('service_container')->has('swiftmailer.mailer.default')) {
            $swiftmailerAdapter = new ChildDefinition('sylius.email_sender.adapter.abstract');
            $swiftmailerAdapter->setClass(SwiftMailerAdapter::class);
            $swiftmailerAdapter->setArguments([new Reference('swiftmailer.mailer.default'), new Reference('event_dispatcher')]);
            $swiftmailerAdapter->setPrivate(false);

            $container->setDefinition('sylius.email_sender.adapter.swiftmailer', $swiftmailerAdapter);

            return 'sylius.email_sender.adapter.swiftmailer';
        }

        return 'sylius.email_sender.adapter.default';
    }

    private function configureRendererAdapter(ContainerBuilder $container, array $config): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        $defaultRendererAdapter = 'sylius.email_renderer.adapter.default';
        if (array_key_exists('TwigBundle', $bundles)) {
            $twigAdapter = new ChildDefinition('sylius.email_renderer.adapter.abstract');
            $twigAdapter->setClass(EmailTwigAdapter::class);
            $twigAdapter->setArguments([new Reference('twig'), new Reference('event_dispatcher')]);
            $twigAdapter->setPrivate(false);

            $container->setDefinition('sylius.email_renderer.adapter.twig', $twigAdapter);
            $defaultRendererAdapter = 'sylius.email_renderer.adapter.twig';
        }

        $container->setAlias('sylius.email_renderer.adapter', $config['renderer_adapter'] ?? $defaultRendererAdapter);
    }
}
