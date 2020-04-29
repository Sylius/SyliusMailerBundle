# SyliusMailerBundle

Sending customizable e-mails has never been easier in Symfony.

You can configure different e-mail types in the YAML or in database. (and use YAML as fallback)
This allows you to send out e-mails with one simple method call, providing an unique code and data.

The bundle supports adapters, by default e-mails are rendered using Twig and sent via Swiftmailer, but you can easily implement your own adapter and delegate the whole operation to external API.

This bundle provides easy integration of the [Sylius mailer component](https://docs.sylius.com/en/latest/components_and_bundles/components/Mailer/index.html)
with any Symfony full-stack application.

* [installation](installation.md)
* [your first email](your_first_email.md)
* [using custom adapter](using_custom_adapter.md)
* [configuration](configuration.md)
    
## Learn more

* [Emails in the Sylius platform](https://docs.sylius.com/en/latest/book/architecture/emails.html) - concept documentation
