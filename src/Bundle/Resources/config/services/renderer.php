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

use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailDefaultAdapter;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius.email_renderer.adapter.abstract', AbstractAdapter::class)
        ->abstract()
        ->call('setEventDispatcher', [service('event_dispatcher')->ignoreOnInvalid()]);

    $services->set('sylius.email_renderer.adapter.default', EmailDefaultAdapter::class)
        ->parent('sylius.email_renderer.adapter.abstract')
        ->public();

    $services->set('sylius.email_renderer.adapter.twig', EmailTwigAdapter::class)
        ->parent('sylius.email_renderer.adapter.abstract')
        ->public()
        ->args([
            service('twig'),
            service('event_dispatcher')->nullOnInvalid(),
        ]);
};
