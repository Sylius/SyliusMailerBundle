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

return static function (ContainerConfigurator $container): void {
    $container->import('services/factory.php');
    $container->import('services/provider.php');
    $container->import('services/renderer.php');
    $container->import('services/sender.php');
    $container->import('services/modifier.php');
    $container->import('services/console.php');
};
