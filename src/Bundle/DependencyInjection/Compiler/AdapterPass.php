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

namespace Sylius\Bundle\MailerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AdapterPass implements CompilerPassInterface
{
    public function processAdapters(
        ContainerBuilder $container,
        string $adapterAlias,
        string $defaultAdapterId,
        array $adaptersWithDependency,
    ): void {
        foreach ($adaptersWithDependency as $adapter => $dependency) {
            if (!$container->has($dependency)) {
                $container->removeDefinition($adapter);
            }
        }

        if ($container->hasAlias($adapterAlias)) {
            return;
        }

        $defaultAdapters = array_keys($adaptersWithDependency);
        $defaultAdapters[] = $defaultAdapterId;

        foreach ($defaultAdapters as $adapter) {
            if ($container->hasDefinition($adapter)) {
                $container->setAlias($adapterAlias, new Alias($adapter, true));

                return;
            }
        }
    }
}
