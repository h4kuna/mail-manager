<?php

namespace h4kuna\MailManager;

use h4kuna\MailManager\Mailer\FileMailer,
    Nette\Mail,
    Tester\Assert,
    Tester\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

class MailManagerTest extends TestCase
{

    /** @var MailManager */
    private $mailManager;

    /** @var FileMailer */
    private $mailer;

    function __construct(MailManager $mailManager, FileMailer $mailer)
    {
        $this->mailManager = $mailManager;
        $this->mailer = $mailer;
    }

    public function testSend()
    {
        $emails = array('foo@example.com' => 'Hello world', 'bar@example.com' => 'test');
        foreach ($emails as $email => $body) {
            /* @var $message Mail\Message */
            $message = $this->mailManager->createMessage($body, array('variable' => 'Hello'))
                    ->addTo($email);
            /**
             * Check returned object
             */
            Assert::true($message instanceof Mail\Message);
            $this->mailManager->send();

            /**
             * Check temporary file
             */
            Assert::true(file_exists($this->mailer->getLastFile()));
            Assert::true(filesize($this->mailer->getLastFile()) > 0);
        }
    }

    public function testParseSystemMail()
    {
        $this->mailManager->createSystemMail(file_get_contents(__DIR__ . '/template/system-mail.eml'));
        $this->mailManager->send();
        Assert::true(file_exists($this->mailer->getLastFile()));
        Assert::true(filesize($this->mailer->getLastFile()) > 0);
    }

}

/* @var $mailManager MailManager */
$mailManager = $container->getByType('h4kuna\MailManager\MailManager');

/* @var $mailer FileMailer */
$mailer = $container->getByType('h4kuna\MailManager\Mailer\FileMailer');

$test = new MailManagerTest($mailManager, $mailer);
$test->run();

