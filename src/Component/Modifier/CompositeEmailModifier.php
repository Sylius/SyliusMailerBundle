<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Modifier;

use Sylius\Component\Mailer\Model\EmailInterface;

final class CompositeEmailModifier implements EmailModifierInterface
{
    /**
     * @param EmailModifierInterface[] $emailModifiers
     */
    public function __construct(
        private iterable $emailModifiers = []
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
