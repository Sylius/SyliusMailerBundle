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

namespace Sylius\Bundle\MailerBundle\Tests\Unit\Bundle\Renderer\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class EmailTwigAdapterTest extends TestCase
{
    private Environment&MockObject $twig;

    private EventDispatcherInterface&MockObject $dispatcher;

    private EmailTwigAdapter $adapter;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->adapter = new EmailTwigAdapter($this->twig, $this->dispatcher);
    }

    #[Test]
    public function it_is_an_adapter(): void
    {
        $this->assertInstanceOf(AbstractAdapter::class, $this->adapter);
    }

    #[Test]
    public function it_creates_and_renders_an_email_without_template(): void
    {
        $email = new Email();
        $email->setSubject('Hello {{ name }}');
        $email->setContent('Welcome {{ name }}!');

        $renderedEmail = new RenderedEmail('Hello World', 'Welcome World!');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(EmailRenderEvent::class),
                SyliusMailerEvents::EMAIL_PRE_RENDER,
            )
            ->willReturnCallback(function (EmailRenderEvent $event) use ($renderedEmail) {
                return new EmailRenderEvent($renderedEmail);
            });

        $result = $this->adapter->render($email, ['name' => 'World']);

        $this->assertInstanceOf(RenderedEmail::class, $result);
    }

    #[Test]
    public function it_renders_an_email_with_template(): void
    {
        $twig = new Environment(new ArrayLoader([
            'MyTemplate' => '{% block subject %}Test Subject{% endblock %}{% block body %}Test Body{% endblock %}',
        ]));
        $adapter = new EmailTwigAdapter($twig, $this->dispatcher);

        $email = new Email();
        $email->setTemplate('MyTemplate');

        $renderedEmail = new RenderedEmail('Test Subject', 'Test Body');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(EmailRenderEvent::class),
                SyliusMailerEvents::EMAIL_PRE_RENDER,
            )
            ->willReturnCallback(function (EmailRenderEvent $event) use ($renderedEmail) {
                return new EmailRenderEvent($renderedEmail);
            });

        $result = $adapter->render($email, []);

        $this->assertInstanceOf(RenderedEmail::class, $result);
    }
}
