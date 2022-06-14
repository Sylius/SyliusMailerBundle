<?php

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\tests\Purger;

final class SentMessagesPurger
{
    public function __construct(private string $spoolDirectory)
    {
    }

    public function purge(): void
    {
        $files = glob($this->spoolDirectory.'/*');
        foreach($files as $file){
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
