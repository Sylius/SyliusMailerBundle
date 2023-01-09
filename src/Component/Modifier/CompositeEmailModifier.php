<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Mailer\Modifier;

use Sylius\Component\Mailer\Model\EmailInterface;

final class CompositeEmailModifier implements EmailModifierInterface
{
    /**
     * @param EmailModifierInterface[] $emailModifiers
     */
    public function __construct(
        private iterable $emailModifiers = [],
    ) {
    }

    public function modify(EmailInterface $email, array $factors = []): EmailInterface
    {
        foreach ($this->emailModifiers as $modifier) {
            $email = $modifier->modify($email, $factors);
        }

        return $email;
    }
}
