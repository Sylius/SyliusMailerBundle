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

namespace Sylius\Bundle\MailerBundle\Cli\Dumper;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmailsListDumper implements DumperInterface
{
    public function __construct(
        private array $emails,
        private TranslatorInterface $translator,
    ) {
    }

    public function dump(InputInterface $input, OutputInterface $output, array $data = []): void
    {
        $io = new SymfonyStyle($input, $output);
        $rows = [];

        foreach ($this->emails as $code => $emailConfiguration) {
            $rows[] = [
                $code,
                $emailConfiguration['template'],
                $emailConfiguration['enabled'] ? 'yes' : 'no',
                isset($emailConfiguration['subject']) ? $this->translator->trans($emailConfiguration['subject']) : '',
            ];
        }

        $io->section('<info>Emails</info>');

        $table = new Table($output);
        $table->setHeaders(['Code', 'Template', 'Enabled', 'Subject']);
        $table->setRows($rows);
        $table->render();
    }
}
