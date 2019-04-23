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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('sylius_mailer');

            /** @var ArrayNodeDefinition $rootNodeDefinition */
            $rootNodeDefinition = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();

            /** @var ArrayNodeDefinition $rootNodeDefinition */
            $rootNodeDefinition = $treeBuilder->root('sylius_mailer');
        }

        $rootNodeDefinition->children()->scalarNode('sender_adapter')->defaultValue('sylius.email_sender.adapter.swiftmailer');
        $rootNodeDefinition->children()->scalarNode('renderer_adapter')->defaultValue('sylius.email_renderer.adapter.twig');

        $this->addEmailsSection($rootNodeDefinition);

        return $treeBuilder;
    }

    private function addEmailsSection(ArrayNodeDefinition $nodeDefinition): void
    {
        /** @var ArrayNodeDefinition $senderNodeDefinition */
        $senderNodeDefinition = $nodeDefinition->children()->arrayNode('sender')->addDefaultsIfNotSet();

        $senderNodeDefinition->children()->scalarNode('name')->defaultValue('Example.com Store');
        $senderNodeDefinition->children()->scalarNode('address')->defaultValue('no-reply@example.com');

        /** @var ArrayNodeDefinition $emailsNodeDefinition */
        $emailsNodeDefinition = $nodeDefinition->children()->arrayNode('emails')->useAttributeAsKey('code');

        /** @var ArrayNodeDefinition $emailNodeDefinition */
        $emailNodeDefinition = $emailsNodeDefinition->arrayPrototype();

        $emailNodeDefinition->children()->scalarNode('subject')->cannotBeEmpty();
        $emailNodeDefinition->children()->scalarNode('template')->cannotBeEmpty();
        $emailNodeDefinition->children()->scalarNode('enabled')->defaultTrue();

        /** @var ArrayNodeDefinition $emailSenderNodeDefinition */
        $emailSenderNodeDefinition = $emailNodeDefinition->children()->arrayNode('sender');

        $emailSenderNodeDefinition->children()->scalarNode('name');
        $emailSenderNodeDefinition->children()->scalarNode('address');
    }
}
