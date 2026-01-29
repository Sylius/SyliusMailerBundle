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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\MailerBundle\Console\Command\DebugMailerCommand;
use Sylius\Bundle\MailerBundle\Console\Command\Dumper\EmailDetailDumperInterface;
use Sylius\Bundle\MailerBundle\Console\Command\Dumper\EmailDetailsDumper;
use Sylius\Bundle\MailerBundle\Console\Command\Dumper\EmailsListDumper;
use Sylius\Bundle\MailerBundle\Console\Command\Dumper\SenderDataDumper;
use Symfony\Contracts\Translation\TranslatorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set(EmailDetailDumperInterface::class, EmailDetailsDumper::class)
        ->args([
            '%sylius.mailer.emails%',
            service(TranslatorInterface::class)->nullOnInvalid(),
            service('twig.loader'),
        ]);

    $services->set(EmailsListDumper::class)
        ->args([
            '%sylius.mailer.emails%',
            service(TranslatorInterface::class)->nullOnInvalid(),
        ])
        ->tag('sylius_mailer.dumper');

    $services->set(SenderDataDumper::class)
        ->args([
            '%sylius.mailer.sender_name%',
            '%sylius.mailer.sender_address%',
        ])
        ->tag('sylius_mailer.dumper');

    $services->set(DebugMailerCommand::class)
        ->autoconfigure()
        ->args([
            tagged_iterator('sylius_mailer.dumper'),
            service(EmailDetailDumperInterface::class),
        ]);
};
