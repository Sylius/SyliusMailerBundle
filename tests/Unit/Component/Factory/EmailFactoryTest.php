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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Component\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\Email;

final class EmailFactoryTest extends TestCase
{
    private EmailFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new EmailFactory();
    }

    public function testImplementsEmailFactoryInterface(): void
    {
        $this->assertInstanceOf(EmailFactoryInterface::class, $this->factory);
    }

    public function testCreatesNewEmail(): void
    {
        $email = $this->factory->createNew();

        $this->assertInstanceOf(Email::class, $email);
    }
}
