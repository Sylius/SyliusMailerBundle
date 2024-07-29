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

namespace Sylius\Bundle\MailerBundle\Renderer\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EmailDefaultAdapter extends AbstractAdapter
{
    /** @var EventDispatcherInterface|null */
    protected $dispatcher;

    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        throw new \RuntimeException(sprintf(
            'You need to configure an adapter to render the email. Take a look at %s (requires "symfony/twig-bundle" library).',
            EmailTwigAdapter::class,
        ));
    }
}
