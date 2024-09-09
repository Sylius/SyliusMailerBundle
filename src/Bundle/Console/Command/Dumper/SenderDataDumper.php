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

namespace Sylius\Bundle\MailerBundle\Console\Command\Dumper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SenderDataDumper implements DumperInterface
{
    public function __construct(
        private readonly string $senderName,
        private readonly string $senderEmail,
    ) {
    }

    public function dump(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('<info>Sender</info>');
        $io->horizontalTable(['Name', 'Email'], [[$this->senderName, $this->senderEmail]]);
    }
}
