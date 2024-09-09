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

namespace Sylius\Bundle\MailerBundle\tests\Integration\Cli;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DebugMailerCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('sylius:debug:mailer');
        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function it_lists_all_configured_emails_and_sender_data(): void
    {
        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Name    Sender', $output);
        $this->assertStringContainsString('Email   sender@example.com', $output);
        $this->assertStringContainsString(
            '| test_email           | Email/test.html.twig         | yes     | Hardcoded subject           |',
            $output,
        );
        $this->assertStringContainsString(
            '| test_email_with_data | Email/testWithData.html.twig | yes     | Subject for email with data |',
            $output,
        );
        $this->assertStringContainsString(
            '| test_modified_email  | Email/test.html.twig         | yes     |',
            $output,
        );
        $this->assertStringContainsString(
            '| test_disabled_email  | Email/test.html.twig         | no      |',
            $output,
        );
    }

    /** @test */
    public function it_shows_configured_email_details(): void
    {
        $this->commandTester->execute(['codeOfEmail' => 'test_email_with_data']);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Email: test_email_with_data', $output);
        $this->assertStringContainsString('Subject: Subject for email with data', $output);
        $this->assertStringContainsString('Enabled: yes', $output);
        $this->assertStringContainsString('Test email body. Data: {{ data }}.', $output);
    }
}
