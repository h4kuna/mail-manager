MailManager
-----------
[![Build Status](https://travis-ci.org/h4kuna/mail-manager.svg?branch=master)](https://travis-ci.org/h4kuna/mail-manager)

This extension for [Nette framework 2.4+](http://nette.org/). Support testing mails

Installation to project
-----------------------
```sh
$ composer require h4kuna/mail-manager
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

    # optional
	plainMacro: # where will find email like plain text alternative default: %file%-plain
    assetsDir: # path to assets
    returnPath: # where back mail whose send non exists mail
    messageFactory: # prepare for Message instance

    # development
    development: # enable FileMailer whose save email to file
    tempDir: # where save email to file
    live: # how long live email file in temp directory
        # - FALSE - forever
        # - '+1 minute' - relative time (default)
```

Support different templates for plain text and for html.
```php
$message = $mailer->createMessage('body', ['foo' => $foo, 'bar' => $bar]);

// if you have body.latte (for html) and body-plain.latte (for plain text) in same directory, then is used. And bind variables onetime.

$mailer->send($message);
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
$message->addBcc('bar@example.com'); // avaible is 'mail' or 'name <mail>'
$mailer->send($message); // if anything bad throw exception
```

Features
--------
- display email as html page
- on development machine default save to file
- autoremove saved email
- if path name containt word plain, than set plain text mail to send
- parse system mail and send, if you haven't installed sendmail on server

