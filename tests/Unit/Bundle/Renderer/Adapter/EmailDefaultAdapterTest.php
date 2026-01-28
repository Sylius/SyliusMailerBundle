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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Bundle\Renderer\Adapter;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailDefaultAdapter;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;

final class EmailDefaultAdapterTest extends TestCase
{
    private EmailDefaultAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new EmailDefaultAdapter();
    }

    public function testIsAnAdapter(): void
    {
        $this->assertInstanceOf(AbstractAdapter::class, $this->adapter);
    }

    public function testThrowsExceptionAboutNotConfiguredEmailRendererAdapter(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'You need to configure an adapter to render the email. Take a look at %s (requires "symfony/twig-bundle" library).',
            EmailTwigAdapter::class,
        ));

        $this->adapter->render(new Email(), []);
    }
}
