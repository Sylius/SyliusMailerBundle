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

namespace spec\Sylius\Component\Mailer\Modifier;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;

final class CompositeEmailModifierSpec extends ObjectBehavior
{
    function let(EmailModifierInterface $firstEmailModifier, EmailModifierInterface $secondEmailModifier): void
    {
        $this->beConstructedWith([$firstEmailModifier, $secondEmailModifier]);
    }

    function it_implements_email_modifier_interface(): void
    {
        $this->shouldImplement(EmailModifierInterface::class);
    }

    function it_uses_all_email_modifiers_to_modify_the_email(
        EmailModifierInterface $firstEmailModifier,
        EmailModifierInterface $secondEmailModifier,
        EmailInterface $email,
    ) {
        $firstEmailModifier->modify($email, ['factor' => 'value'])->shouldBeCalled()->willReturn($email);
        $secondEmailModifier->modify($email, ['factor' => 'value'])->shouldBeCalled()->willReturn($email);

        $this->modify($email, ['factor' => 'value'])->shouldReturn($email);
    }
}
