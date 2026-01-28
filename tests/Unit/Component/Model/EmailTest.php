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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Component\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;

final class EmailTest extends TestCase
{
    private Email $email;

    protected function setUp(): void
    {
        $this->email = new Email();
    }

    public function testImplementsEmailInterface(): void
    {
        $this->assertInstanceOf(EmailInterface::class, $this->email);
    }

    public function testHasNoCodeByDefault(): void
    {
        $this->assertNull($this->email->getCode());
    }

    public function testCodeIsMutable(): void
    {
        $this->email->setCode('bar');

        $this->assertSame('bar', $this->email->getCode());
    }

    public function testSubjectIsMutable(): void
    {
        $this->email->setSubject('foo');

        $this->assertSame('foo', $this->email->getSubject());
    }

    public function testContentIsMutable(): void
    {
        $this->email->setContent('foo content');

        $this->assertSame('foo content', $this->email->getContent());
    }

    public function testTemplateIsMutable(): void
    {
        $this->email->setTemplate('template.html.twig');

        $this->assertSame('template.html.twig', $this->email->getTemplate());
    }

    public function testSenderNameIsMutable(): void
    {
        $this->email->setSenderName('Example');

        $this->assertSame('Example', $this->email->getSenderName());
    }

    public function testSenderAddressIsMutable(): void
    {
        $this->email->setSenderAddress('no-reply@example.com');

        $this->assertSame('no-reply@example.com', $this->email->getSenderAddress());
    }

    public function testIsEnabledByDefault(): void
    {
        $this->assertTrue($this->email->isEnabled());
    }

    public function testCanBeDisabled(): void
    {
        $this->email->setEnabled(false);

        $this->assertFalse($this->email->isEnabled());
    }
}
