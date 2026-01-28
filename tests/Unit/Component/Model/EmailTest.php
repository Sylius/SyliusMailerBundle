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

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_implements_email_interface(): void
    {
        $this->assertInstanceOf(EmailInterface::class, $this->email);
    }

    #[Test]
    public function it_has_no_code_by_default(): void
    {
        $this->assertNull($this->email->getCode());
    }

    #[Test]
    public function its_code_is_mutable(): void
    {
        $this->email->setCode('bar');

        $this->assertSame('bar', $this->email->getCode());
    }

    #[Test]
    public function its_subject_is_mutable(): void
    {
        $this->email->setSubject('foo');

        $this->assertSame('foo', $this->email->getSubject());
    }

    #[Test]
    public function its_content_is_mutable(): void
    {
        $this->email->setContent('foo content');

        $this->assertSame('foo content', $this->email->getContent());
    }

    #[Test]
    public function its_template_is_mutable(): void
    {
        $this->email->setTemplate('template.html.twig');

        $this->assertSame('template.html.twig', $this->email->getTemplate());
    }

    #[Test]
    public function its_sender_name_is_mutable(): void
    {
        $this->email->setSenderName('Example');

        $this->assertSame('Example', $this->email->getSenderName());
    }

    #[Test]
    public function its_sender_address_is_mutable(): void
    {
        $this->email->setSenderAddress('no-reply@example.com');

        $this->assertSame('no-reply@example.com', $this->email->getSenderAddress());
    }

    #[Test]
    public function it_is_enabled_by_default(): void
    {
        $this->assertTrue($this->email->isEnabled());
    }

    #[Test]
    public function it_can_be_disabled(): void
    {
        $this->email->setEnabled(false);

        $this->assertFalse($this->email->isEnabled());
    }
}
