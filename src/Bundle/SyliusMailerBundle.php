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

namespace Sylius\Bundle\MailerBundle;

use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\RendererAdapterPass;
use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\SenderAdapterPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusMailerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SenderAdapterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -256);
        $container->addCompilerPass(new RendererAdapterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -256);
    }
}
