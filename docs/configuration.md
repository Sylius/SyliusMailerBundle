# Configuration reference

```yaml
sylius_mailer:
      sender_adapter: sylius.email_sender.adapter.symfony_mailer # Adapter for sending e-mails.
      renderer_adapter: sylius.email_renderer.adapter.twig # Adapter for rendering e-mails.
      sender:
          name: # Required - default sender name.
          address: # Required - default sender e-mail address.
      templates: # Your templates available for selection in backend!
          label1: Template path 1
          label2: Template path 2
          label3: Template path 3
      emails:
          your_email:
              subject: Subject of your email
              template: App:Email:yourEmail.html.twig
              enabled: true/false
              sender:
                 name: Custom name
                 address: Custom sender address for this e-mail
          your_another_email:
              subject: Subject of your another email
              template: App:Email:yourAnotherEmail.html.twig
              enabled: true/false
              sender:
                 name: Custom name
                 address: Custom sender address for this e-mail
```
**[Go back to the documentation's index](index.md)**
