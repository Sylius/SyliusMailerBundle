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

namespace Sylius\Bundle\MailerBundle\Cli;

use Sylius\Bundle\MailerBundle\Cli\Dumper\DumperInterface;
use Sylius\Bundle\MailerBundle\Cli\Dumper\EmailDetailDumperInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'sylius:debug:mailer', description: 'Shows configured emails and sender data')]
final class DebugMailerCommand extends Command
{
    public function __construct(
        /** @var DumperInterface[] $dumpers */
        private iterable $dumpers,
        private EmailDetailDumperInterface $emailDetailDumper,
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
            foreach ($this->dumpers as $dumper) {
                $dumper->dump($input, $output);
            }

            return 0;
        }

        /** @var string $email */
        $email = $input->getArgument('email');
        $this->emailDetailDumper->dump($input, $output, $email);

        return 0;
    }
}
