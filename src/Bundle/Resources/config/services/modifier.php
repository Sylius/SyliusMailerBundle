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

use Sylius\Component\Mailer\Modifier\CompositeEmailModifier;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set(EmailModifierInterface::class, CompositeEmailModifier::class)
        ->args([
            tagged_iterator('sylius_mailer.email_modifier'),
        ]);
};
