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

namespace App\Mailer;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

final class FileTransport implements TransportInterface
{
    public function __construct(private string $spoolDirectory)
    {
    }

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        touch($this->spoolDirectory . '/' . (string) rand(10000, 1000000) . '.json');
        file_put_contents($this->spoolDirectory . '/' . (string) rand(10000, 1000000) . '.json', serialize($message));

        return new SentMessage($message, $envelope);
    }

    public function __toString(): string
    {
        return 'file';
    }
}
