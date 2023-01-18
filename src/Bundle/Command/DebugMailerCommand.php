<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'sylius:debug:mailer', description: 'Shows configured emails and sender data')]
final class DebugMailerCommand extends Command
{
    public function __construct(
        private string $senderName,
        private string $senderEmail,
        private array $emails,
        private string $templatesDir,
        private TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL, 'Email to be shown');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('email') === null) {
            $this->dumpSenderData($input, $output);
            $this->dumpListOfEmails($input, $output);

            return 0;
        }

        /** @var string $email */
        $email = $input->getArgument('email');

        $this->dumpEmailDetails($input, $output, $email);

        return 0;
    }

    private function dumpSenderData(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('<info>Sender</info>');
        $io->horizontalTable(['Name', 'Email'], [[$this->senderName, $this->senderEmail]]);
    }

    private function dumpListOfEmails(InputInterface $input, OutputInterface $output): void
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

    private function dumpEmailDetails(InputInterface $input, OutputInterface $output, string $code): void
    {
        $email = $this->emails[$code];

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('<fg=cyan>Email:</> %s', $code));
        $io->writeln(sprintf('<comment>Subject:</comment> %s', $this->translator->trans($email['subject'] ?? '')));
        $io->writeln(sprintf('<comment>Enabled:</comment> %s', $email['enabled'] ? '<info>yes</info>' : '<error>no</error>'));
        $io->newLine();
        $io->text(file_get_contents($this->templatesDir.'/'.$email['template']));
    }
}
