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

namespace spec\Sylius\Bundle\MailerBundle\Renderer\Adapter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;

final class EmailDefaultAdapterSpec extends ObjectBehavior
{
    function it_is_an_adapter(): void
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_throws_an_exception_about_not_configured_email_renderer_adapter(EmailInterface $email): void
    {
        $this
            ->shouldThrow(new \RuntimeException(sprintf(
                'You need to configure an adapter to render the email. Take a look at %s (requires "symfony/twig-bundle" library).',
                EmailTwigAdapter::class,
            )))
            ->during('render', [$email, []])
        ;
    }
}
