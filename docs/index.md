# SyliusMailerBundle

Sending customizable e-mails has never been easier in Symfony.

You can configure different e-mail types in the YAML or in database. (and use YAML as fallback)
This allows you to send out e-mails with one simple method call, providing an unique code and data.

The bundle supports adapters, by default e-mails are rendered using Twig and sent via Symfony Mailer, but you can easily implement your own adapter and delegate the whole operation to external API.

This bundle provides easy integration of the [Sylius mailer component](https://docs.sylius.com/en/latest/components_and_bundles/components/Mailer/index.html)
with any Symfony full-stack application.

* [Installation](installation.md)
* [Your First Email](your_first_email.md)
* [Using Custom Adapter](using_custom_adapter.md)
* [Configuration reference](configuration.md)
    
## Learn more

* [Emails in the Sylius platform](https://docs.sylius.com/en/latest/book/architecture/emails.html) - concept documentation
