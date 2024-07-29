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

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SenderAdapterPass extends AdapterPass
{
    public const ADAPTER_ALIAS = 'sylius.email_sender.adapter';

    public const DEFAULT_ADAPTER = 'sylius.email_sender.adapter.default';

    public function process(ContainerBuilder $container): void
    {
        $this->processAdapters($container, self::ADAPTER_ALIAS, self::DEFAULT_ADAPTER, [
            'sylius.email_sender.adapter.symfony_mailer' => 'mailer.mailer',
        ]);
    }
}
