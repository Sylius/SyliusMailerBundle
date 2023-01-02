<?php

declare(strict_types=1);

namespace Sylius\Component\Mailer\Modifier;

use Sylius\Component\Mailer\Model\EmailInterface;

interface EmailModifierInterface
{
    public function modify(EmailInterface $email, array $factors = []): EmailInterface;
}
