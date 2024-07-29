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

namespace Sylius\Bundle\MailerBundle\DependencyInjection;

use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\RendererAdapterPass;
use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\SenderAdapterPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

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
        if (isset($mergedConfig['sender_adapter'])) {
            $container->setAlias(SenderAdapterPass::ADAPTER_ALIAS, new Alias($mergedConfig['sender_adapter'], true));
        }
    }

    private function configureRendererAdapter(array $mergedConfig, ContainerBuilder $container): void
    {
        if (isset($mergedConfig['renderer_adapter'])) {
            $container->setAlias(RendererAdapterPass::ADAPTER_ALIAS, new Alias($mergedConfig['renderer_adapter'], true));
        }
    }
}
