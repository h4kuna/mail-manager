<?php

use h4kuna\MailManager;
use Nette\Mail\Message;
use Tester\Assert;

$container = require __DIR__ . '/bootstrap.php';

/* @var $mailManager MailManager */
$mailManager = $container->getService('mailManagerExtension.mailManager');

/* @var $mailer MailManager\Mailer */
$mailer = $container->getService('mailManagerExtension.developmentMailer');

$emails = array('foo@example.com' => 'Hello world', 'bar@example.com' => 'test');
foreach ($emails as $email => $body) {
    /* @var $message Message */
    $message = $mailManager->createMessage($email, $body, array('variable' => 'Hello'));
    /**
     * Check returned object
     */
    Assert::true($message instanceof Message);
    $mailManager->send();

    /**
     * Check temporary file
     */
    Assert::true(file_exists($mailer->getLastFile()));
    Assert::true(filesize($mailer->getLastFile()) > 0);
}

/**
 * Parse system message and you can send via smtp
 */
$mailManager->sendSystemMail(file_get_contents(__DIR__ . '/template/system-mail.eml'));


