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

namespace Sylius\Component\Mailer\Sender\Adapter;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /** @var EventDispatcherInterface|null */
    protected $dispatcher;

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }
}
