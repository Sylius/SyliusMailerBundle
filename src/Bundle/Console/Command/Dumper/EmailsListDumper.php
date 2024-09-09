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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmailsListDumper implements DumperInterface
{
    public function __construct(
        private readonly array $emails,
        private readonly ?TranslatorInterface $translator,
    ) {
    }

    public function dump(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $rows = [];

        foreach ($this->emails as $code => $emailConfiguration) {
            $subject = $emailConfiguration['subject'] ?? '';

            if ($this->translator !== null) {
                $subject = $this->translator->trans($subject);
            }

            $rows[] = [
                $code,
                $emailConfiguration['template'],
                $emailConfiguration['enabled'] ? 'yes' : 'no',
                $subject,
            ];
        }

        $io->section('<info>Emails</info>');

        $table = new Table($output);
        $table->setHeaders(['Code', 'Template', 'Enabled', 'Subject']);
        $table->setRows($rows);
        $table->render();
    }
}
