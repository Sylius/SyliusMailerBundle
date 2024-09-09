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

namespace Sylius\Bundle\MailerBundle\Console\Command;

use Sylius\Bundle\MailerBundle\Console\Command\Dumper\DumperInterface;
use Sylius\Bundle\MailerBundle\Console\Command\Dumper\EmailDetailDumperInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'sylius:debug:mailer', description: 'Debug email messages')]
final class DebugMailerCommand extends Command
{
    public function __construct(
        /** @var DumperInterface[] $dumpers */
        private readonly iterable $dumpers,
        private readonly EmailDetailDumperInterface $emailDetailDumper,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('codeOfEmail', InputArgument::OPTIONAL, 'Expected email to be shown identified by its code');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('codeOfEmail') === null) {
            return $this->dumpAllEmails($input, $output);
        }

        return $this->dumpEmailDetails($input, $output);
    }

    private function dumpAllEmails(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->dumpers as $dumper) {
            $dumper->dump($input, $output);
        }

        return Command::SUCCESS;
    }

    private function dumpEmailDetails(InputInterface $input, OutputInterface $output): int
    {
        $codeOfEmail = $input->getArgument('codeOfEmail');

        Assert::string($codeOfEmail);

        $this->emailDetailDumper->dump($codeOfEmail, $input, $output);

        return Command::SUCCESS;
    }
}
