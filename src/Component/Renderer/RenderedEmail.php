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

namespace Sylius\Component\Mailer\Renderer;

class RenderedEmail
{
    /** @var string */
    protected $subject;

    /** @var string */
    protected $body;

    /** @var string|null */
    protected $bodyPlaintext;

    public function __construct(string $subject, string $body, string $bodyPlaintext = null)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->bodyPlaintext = $bodyPlaintext;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBodyPlaintext(): ?string
    {
        return $this->bodyPlaintext;
    }

    public function setBodyPlaintext(string $body): void
    {
        $this->bodyPlaintext = $body;
    }
}
