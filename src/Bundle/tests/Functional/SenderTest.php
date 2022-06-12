<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\tests\Functional;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class SenderTest extends KernelTestCase
{
    private SenderInterface $sender;

    private string $spoolDirectory;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $this->sender = $container->get('sylius.email_sender');
        $this->spoolDirectory = $container->getParameter('kernel.cache_dir') . '/spool/default';
    }

    /** @test */
    public function it_sends_email_rendered_with_given_template(): void
    {
        $this->sender->send('test_email', ['test@example.com']);

        $this->assertEquals(1, iterator_count(new \FilesystemIterator($this->spoolDirectory, \FilesystemIterator::SKIP_DOTS)));

        $message = $this->getMessage();
        $this->assertStringContainsString('Test email body', unserialize($message->getContents())->getBody());
    }

    protected function tearDown(): void
    {
        $files = glob($this->spoolDirectory.'/*');
        foreach($files as $file){
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function getMessage(): SplFileInfo
    {
        $finder = new Finder();
        $finder->files()->name('*.message')->in($this->spoolDirectory);

        return array_values(iterator_to_array($finder))[0];
    }
}
