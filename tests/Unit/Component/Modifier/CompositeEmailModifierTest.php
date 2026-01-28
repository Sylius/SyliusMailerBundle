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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Component\Modifier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Modifier\CompositeEmailModifier;
use Sylius\Component\Mailer\Modifier\EmailModifierInterface;

final class CompositeEmailModifierTest extends TestCase
{
    public function testImplementsEmailModifierInterface(): void
    {
        $modifier = new CompositeEmailModifier([]);

        $this->assertInstanceOf(EmailModifierInterface::class, $modifier);
    }

    public function testUsesAllEmailModifiersToModifyTheEmail(): void
    {
        $email = new Email();

        /** @var EmailModifierInterface&MockObject $firstEmailModifier */
        $firstEmailModifier = $this->createMock(EmailModifierInterface::class);
        /** @var EmailModifierInterface&MockObject $secondEmailModifier */
        $secondEmailModifier = $this->createMock(EmailModifierInterface::class);

        $firstEmailModifier
            ->expects($this->once())
            ->method('modify')
            ->with($email, ['factor' => 'value'])
            ->willReturn($email);

        $secondEmailModifier
            ->expects($this->once())
            ->method('modify')
            ->with($email, ['factor' => 'value'])
            ->willReturn($email);

        $compositeModifier = new CompositeEmailModifier([$firstEmailModifier, $secondEmailModifier]);

        $result = $compositeModifier->modify($email, ['factor' => 'value']);

        $this->assertSame($email, $result);
    }
}
