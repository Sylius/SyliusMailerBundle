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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\MailerBundle\Sender\Adapter\DefaultAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Sender\Sender;
use Sylius\Component\Mailer\Sender\SenderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius.email_sender', Sender::class)
        ->args([
            service('sylius.email_renderer.adapter'),
            service('sylius.email_sender.adapter'),
            service('sylius.email_provider'),
            service('sylius.mailer.default_settings_provider'),
            service(EmailModifierInterface::class),
        ]);
    $services->alias(SenderInterface::class, 'sylius.email_sender');

    $services->set('sylius.email_sender.adapter.abstract', AbstractAdapter::class)
        ->abstract()
        ->call('setEventDispatcher', [service('event_dispatcher')->ignoreOnInvalid()]);

    $services->set('sylius.email_sender.adapter.default', DefaultAdapter::class)
        ->parent('sylius.email_sender.adapter.abstract')
        ->public();

    $services->set('sylius.email_sender.adapter.symfony_mailer', SymfonyMailerAdapter::class)
        ->parent('sylius.email_sender.adapter.abstract')
        ->public()
        ->args([
            service('mailer.mailer'),
        ]);
};
