MailManager
-----------
[![Build Status](https://travis-ci.org/h4kuna/exchange.png)](https://travis-ci.org/h4kuna/mail-manager)

This extension for [Nette framework 2.1+](http://nette.org/). Support testing mails

Installation to project
-----------------------
```sh
$ composer require h4kuna/mail-manager @dev
```

Example NEON config
-------------------
look at to tests/config.neon

How to use
----------
Look at to example.php

Features
--------
- display email as html page, call MailManager::createTemplate($body, $data) with same parameters like MailManager::createMessage(..., $body, $data)
- on development machine default save to file
- autoremove saved email, livetime is 1 minute
- if path name containt word plain, than set plain text mail to send
- parse system mail and send, if you haven't installed sendmail on server

Future
------
- macros whose help write email like table
