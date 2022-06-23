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

namespace Sylius\Bundle\MailerBundle\tests\Functional;

use Sylius\Bundle\MailerBundle\tests\Provider\MessagesProvider;
use Sylius\Bundle\MailerBundle\tests\Purger\SentMessagesPurger;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DefaultSenderTest extends KernelTestCase
{
    private SenderInterface $sender;

    private MessagesProvider $messagesProvider;

    private string $spoolDirectory;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $this->sender = $container->get('sylius.email_sender');
        $this->spoolDirectory = $container->getParameter('kernel.cache_dir') . '/spool/default';
        $this->messagesProvider = new MessagesProvider($this->spoolDirectory);
    }

    /** @test */
    public function it_sends_email_rendered_with_given_template(): void
    {
        $this->sender->send('test_email', ['test@example.com']);

        $messages = $this->messagesProvider->getMessages();

        $this->assertCount(1, $messages);
        $this->assertStringContainsString('Test email subject', $messages[0]->getSubject());
        $this->assertStringContainsString('Test email body', $messages[0]->getBody());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        (new SentMessagesPurger($this->spoolDirectory))->purge();
    }
}
