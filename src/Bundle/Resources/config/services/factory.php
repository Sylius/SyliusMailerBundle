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

use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius.factory.email', EmailFactory::class);
    $services->alias(EmailFactoryInterface::class, 'sylius.factory.email');
};
