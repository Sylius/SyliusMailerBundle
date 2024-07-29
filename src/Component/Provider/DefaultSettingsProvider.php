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

namespace Sylius\Component\Mailer\Provider;

final class DefaultSettingsProvider implements DefaultSettingsProviderInterface
{
    public function __construct(private string $senderName, private string $senderAddress)
    {
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getSenderAddress(): string
    {
        return $this->senderAddress;
    }
}
