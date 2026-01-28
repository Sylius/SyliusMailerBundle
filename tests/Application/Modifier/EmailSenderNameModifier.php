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

namespace App\Modifier;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;

final class EmailSenderNameModifier implements EmailModifierInterface
{
    public function modify(EmailInterface $email, array $factors = []): EmailInterface
    {
        if ($email->getCode() === 'test_modified_email') {
            $email->setSenderName('Modified sender name');
        }

        return $email;
    }
}
