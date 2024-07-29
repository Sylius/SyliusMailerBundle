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

use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class EmailTwigAdapter extends AbstractAdapter
{
    /** @var Environment */
    protected $twig;

    /** @var EventDispatcherInterface|null */
    protected $dispatcher;

    public function __construct(Environment $twig, ?EventDispatcherInterface $dispatcher = null)
    {
        $this->twig = $twig;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        $renderedEmail = $this->getRenderedEmail($email, $data);

        $event = new EmailRenderEvent($renderedEmail);

        if ($this->dispatcher !== null) {
            /** @var EmailRenderEvent $event */
            $event = $this->dispatcher->dispatch($event, SyliusMailerEvents::EMAIL_PRE_RENDER);
        }

        return $event->getRenderedEmail();
    }

    private function getRenderedEmail(EmailInterface $email, array $data): RenderedEmail
    {
        if (null !== $email->getTemplate()) {
            return $this->provideEmailWithTemplate($email, $data);
        }

        return $this->provideEmailWithoutTemplate($email, $data);
    }

    /**
     * @psalm-suppress InternalMethod
     */
    private function provideEmailWithTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        $data = $this->twig->mergeGlobals($data);

        $template = $this->twig->load((string) $email->getTemplate())->unwrap();

        $subject = $template->renderBlock('subject', $data);
        $body = $template->renderBlock('body', $data);

        return new RenderedEmail($subject, $body);
    }

    private function provideEmailWithoutTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        $twig = new Environment(new ArrayLoader([]));

        $subjectTemplate = $twig->createTemplate((string) $email->getSubject());
        $bodyTemplate = $twig->createTemplate((string) $email->getContent());

        $subject = $subjectTemplate->render($data);
        $body = $bodyTemplate->render($data);

        return new RenderedEmail($subject, $body);
    }
}
