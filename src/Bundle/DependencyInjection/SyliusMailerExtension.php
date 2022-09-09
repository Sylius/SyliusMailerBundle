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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

final class SyliusMailerExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->configureSenderAdapter($mergedConfig, $container);
        $this->configureRendererAdapter($mergedConfig, $container);

        $container->setParameter('sylius.mailer.sender_name', $mergedConfig['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $mergedConfig['sender']['address']);

        $templates = $mergedConfig['templates'] ?? ['Default' => '@SyliusMailer/default.html.twig'];

        $container->setParameter('sylius.mailer.emails', $mergedConfig['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }

    private function configureSenderAdapter(array $mergedConfig, ContainerBuilder $container): void
    {
        if (!ContainerBuilder::willBeAvailable('symfony/mailer', MailerInterface::class, ['symfony/framework-bundle'])) {
            $container->removeDefinition('sylius.email_sender.adapter.symfony_mailer');
        }

        if (isset($mergedConfig['sender_adapter'])) {
            $container->setAlias('sylius.email_sender.adapter', $mergedConfig['sender_adapter']);

            return;
        }

        $services = [
            'sylius.email_sender.adapter.symfony_mailer',
            'sylius.email_sender.adapter.default',
        ];

        foreach ($services as $service) {
            if ($container->hasDefinition($service)) {
                $container->setAlias('sylius.email_sender.adapter', $service);

                return;
            }
        }
    }

    private function configureRendererAdapter(array $mergedConfig, ContainerBuilder $container): void
    {
        if (!ContainerBuilder::willBeAvailable('twig/twig', Environment::class, ['symfony/twig-bundle'])) {
            $container->removeDefinition('sylius.email_renderer.adapter.twig');
        }

        if (isset($mergedConfig['renderer_adapter'])) {
            $container->setAlias('sylius.email_renderer.adapter', $mergedConfig['renderer_adapter']);

            return;
        }

        $services = [
            'sylius.email_renderer.adapter.twig',
            'sylius.email_renderer.adapter.default',
        ];

        foreach ($services as $service) {
            if ($container->hasDefinition($service)) {
                $container->setAlias('sylius.email_renderer.adapter', $service);

                return;
            }
        }
    }
}
