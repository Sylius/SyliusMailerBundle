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

namespace Sylius\Component\Mailer\Sender;

use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;
use Webmozart\Assert\Assert;

final class Sender implements SenderInterface
{
    private RendererAdapterInterface $rendererAdapter;

    private SenderAdapterInterface $senderAdapter;

    private EmailProviderInterface $provider;

    private DefaultSettingsProviderInterface $defaultSettingsProvider;

    public function __construct(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        EmailProviderInterface $provider,
        DefaultSettingsProviderInterface $defaultSettingsProvider,
    ) {
        $this->senderAdapter = $senderAdapter;
        $this->rendererAdapter = $rendererAdapter;
        $this->provider = $provider;
        $this->defaultSettingsProvider = $defaultSettingsProvider;
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
