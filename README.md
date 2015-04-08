MailManager
-----------
[![Build Status](https://travis-ci.org/h4kuna/mail-manager.svg?branch=master)](https://travis-ci.org/h4kuna/mail-manager)

This extension for [Nette framework 2.1+](http://nette.org/). Support testing mails

Installation to project
-----------------------
```sh
$ composer require h4kuna/mail-manager @dev
```

How to use
----------
Add to your file NEON
```
extensions:
    mailManagerExtension: h4kuna\MailManager\DI\MailManagerExtension

mailManagerExtension:
    from: default@example.com
    templateDir: %appDir%/template # home for mail template
```

Prepare latte file in **$templateDir/test-file.latte**
```html
<strong>variable foo has value:</strong> {$foo}
```

Send mail.
```php
/* @var $mailer h4kuna\MailManager\MailManager */
$message = $mailer->createMessage('test-file', ['foo' => 'bar'])
           ->addTo('Milan Matejcek <milan.matejcek@gmail.com>');
/* @var $message Nette\Mail\Message */
$message->addBc('bar@example.com'); // avaible is 'mail' or 'name <mail>'
$mailer->send(); // if anything bad throw exception
```

Features
--------
- display email as html page, call MailManager::createTemplate($body, $data) with same parameters like MailManager::createMessage(..., $body, $data)
- on development machine default save to file
- autoremove saved email, livetime is 1 minute
- if path name containt word plain, than set plain text mail to send
- parse system mail and send, if you haven't installed sendmail on server

