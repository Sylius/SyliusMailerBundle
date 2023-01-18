<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\Cli\Dumper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SenderDataDumper implements DumperInterface
{
    public function __construct(
        private string $senderName,
        private string $senderEmail,
    ) {
    }

    public function dump(InputInterface $input, OutputInterface $output, array $data = []): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('<info>Sender</info>');
        $io->horizontalTable(['Name', 'Email'], [[$this->senderName, $this->senderEmail]]);
    }
}
