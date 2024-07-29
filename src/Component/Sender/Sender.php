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

namespace Sylius\Component\Mailer\Sender;

use Sylius\Component\Mailer\Modifier\EmailModifierInterface;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Webmozart\Assert\Assert;

final class Sender implements SenderInterface
{
    public function __construct(
        private RendererAdapterInterface $rendererAdapter,
        private SenderAdapterInterface $senderAdapter,
        private EmailProviderInterface $provider,
        private DefaultSettingsProviderInterface $defaultSettingsProvider,
        private ?EmailModifierInterface $emailModifier = null,
    ) {
        if ($this->emailModifier === null) {
            @trigger_error(
                'Not passing EmailModifierInterface is deprecated since 2.1 and will not be possible in 3.0',
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(
        string $code,
        array $recipients,
        array $data = [],
        array $attachments = [],
        array $replyTo = [],
    ): void {
        $arguments = func_get_args();

        Assert::allStringNotEmpty($recipients);

        $email = $this->provider->getEmail($code);
        if ($this->emailModifier !== null) {
            $email = $this->emailModifier->modify($email, $data);
        }

        if (!$email->isEnabled()) {
            return;
        }

        $senderAddress = $email->getSenderAddress() ?: $this->defaultSettingsProvider->getSenderAddress();
        $senderName = $email->getSenderName() ?: $this->defaultSettingsProvider->getSenderName();

        $renderedEmail = $this->rendererAdapter->render($email, $data);

        if (count($arguments) > 5 && $this->senderAdapter instanceof CcAwareAdapterInterface) {
            $this->senderAdapter->sendWithCC(
                $recipients,
                $senderAddress,
                $senderName,
                $renderedEmail,
                $email,
                $data,
                $attachments,
                $replyTo,
                $arguments[5] ?? [],
                $arguments[6] ?? [],
            );

            return;
        }

        $this->senderAdapter->send(
            $recipients,
            $senderAddress,
            $senderName,
            $renderedEmail,
            $email,
            $data,
            $attachments,
            $replyTo,
        );
    }
}
