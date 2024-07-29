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

namespace Sylius\Bundle\MailerBundle\tests\Provider;

use Sylius\Bundle\MailerBundle\tests\Model\SentMessage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Mime\Email;

final class MessagesProvider
{
    public function __construct(private string $spoolDirectory)
    {
    }

    /** @return SentMessage[] */
    public function getMessages(): array
    {
        $finder = new Finder();
        $finder->files()->name('*.message')->in($this->spoolDirectory);

        $messages = array_values(iterator_to_array($finder));
        $parsedMessages = [];

        /** @var SplFileInfo $message */
        foreach ($messages as $message) {
            $contents = unserialize($message->getContents());

            if ($contents instanceof \Swift_Message) {
                $parsedMessages[] = SentMessage::fromSwiftMessage($contents);
            } elseif ($contents instanceof Email) {
                $parsedMessages[] = SentMessage::fromSymfonyMessage($contents);
            }
        }

        return $parsedMessages;
    }
}
