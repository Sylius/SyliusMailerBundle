<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\Cli\Dumper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Loader\LoaderInterface;
use Webmozart\Assert\Assert;

final class EmailDetailsDumper implements DumperInterface
{
    public function __construct(
        private array $emails,
        private TranslatorInterface $translator,
        private LoaderInterface $templateLoader,
    ) {
    }

    public function dump(InputInterface $input, OutputInterface $output, array $data = []): void
    {
        Assert::keyExists($data, 'code');

        $code = $data['code'];
        $email = $this->emails[$code];

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('<fg=cyan>Email:</> %s', $code));
        $io->writeln(sprintf('<comment>Subject:</comment> %s', $this->translator->trans($email['subject'] ?? '')));
        $io->writeln(sprintf('<comment>Enabled:</comment> %s', $email['enabled'] ? '<info>yes</info>' : '<error>no</error>'));
        $io->newLine();
        $io->text($this->templateLoader->getSourceContext($email['template'])->getCode());
    }
}
