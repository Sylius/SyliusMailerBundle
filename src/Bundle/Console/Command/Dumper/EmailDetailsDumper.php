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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Loader\LoaderInterface;
use Webmozart\Assert\Assert;

final class EmailDetailsDumper implements EmailDetailDumperInterface
{
    public function __construct(
        private readonly array $emails,
        private readonly ?TranslatorInterface $translator,
        private readonly LoaderInterface $templateLoader,
    ) {
    }

    public function dump(string $code, InputInterface $input, OutputInterface $output): void
    {
        $email = $this->emails[$code];

        Assert::notNull($email, sprintf('Email with code "%s" does not exist.', $code));

        $subject = $email['subject'] ?? '';

        if ($this->translator !== null) {
            $subject = $this->translator->trans($subject);
        }

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('<fg=cyan>Email:</> %s', $code));
        $io->writeln(sprintf('<comment>Subject:</comment> %s', $subject));
        $io->writeln(sprintf('<comment>Enabled:</comment> %s', $email['enabled'] ? '<info>yes</info>' : '<error>no</error>'));
        $io->newLine();
        $io->text($this->templateLoader->getSourceContext($email['template'])->getCode());
    }
}
