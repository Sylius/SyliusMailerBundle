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

namespace spec\Sylius\Bundle\MailerBundle\Sender\Adapter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;

final class DefaultAdapterSpec extends ObjectBehavior
{
    function it_is_an_adapter(): void
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_throws_an_exception_about_not_configured_email_sender_adapter(
        EmailInterface $email,
        RenderedEmail $renderedEmail,
    ): void {
        $this
            ->shouldThrow(new \RuntimeException(sprintf(
                'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
                SymfonyMailerAdapter::class,
            )))
            ->during('send', [['pawel@sylius.com'], 'arnaud@sylius.com', 'arnaud', $renderedEmail, $email, []])
        ;

        $this
            ->shouldThrow(new \RuntimeException(sprintf(
                'You need to configure an adapter to send the email. Take a look at %s (requires "symfony/mailer" library).',
                SymfonyMailerAdapter::class,
            )))
            ->during('sendWithCc', [['pawel@sylius.com'], 'arnaud@sylius.com', 'arnaud', $renderedEmail, $email, [], [], [], ['cc@example.com'], ['bcc@example.com']])
        ;
    }
}
