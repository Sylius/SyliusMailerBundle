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

namespace Sylius\Component\Mailer\Event;

use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Symfony\Contracts\EventDispatcher\Event;

class EmailRenderEvent extends Event
{
    /**
     * @param string[] $recipients
     */
    public function __construct(
        protected RenderedEmail $renderedEmail,
        protected array $recipients = [],
    ) {
    }

    public function getRenderedEmail(): RenderedEmail
    {
        return $this->renderedEmail;
    }

    public function setRenderedEmail(RenderedEmail $renderedEmail): void
    {
        $this->renderedEmail = $renderedEmail;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }
}
