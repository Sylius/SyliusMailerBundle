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

use Sylius\Component\Mailer\Provider\DefaultSettingsProvider;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProvider;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius.email_provider', EmailProvider::class)
        ->args([
            service('sylius.factory.email'),
            '%sylius.mailer.emails%',
        ]);
    $services->alias(EmailProviderInterface::class, 'sylius.email_provider');

    $services->set('sylius.mailer.default_settings_provider', DefaultSettingsProvider::class)
        ->args([
            '%sylius.mailer.sender_name%',
            '%sylius.mailer.sender_address%',
        ]);
    $services->alias(DefaultSettingsProviderInterface::class, 'sylius.mailer.default_settings_provider');
};
