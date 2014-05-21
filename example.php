<?php

$container = include './tests/bootstrap.php';

/* @var $mailManager \h4kuna\MailManager */
$mailManager = $container->getService('mailManagerExtension.mailManager');

dump($mailManager->createMessage('milan.matejcek@gmail.com', 'ahoj'));
$mailManager->send();
