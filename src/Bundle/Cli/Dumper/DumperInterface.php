<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\Cli\Dumper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface DumperInterface
{
    public function dump(InputInterface $input, OutputInterface $output, array $data = []): void;
}
