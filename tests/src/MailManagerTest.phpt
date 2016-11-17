<?php

namespace h4kuna\MailManager;

use h4kuna\MailManager\Mailer,
	Nette\Mail,
	Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class MailManagerTest extends \Tester\TestCase
{

	/** @var MailManager */
	private $mailManager;

	/** @var Mailer\FileMailer */
	private $mailer;

	function __construct(MailManager $mailManager, Mailer\FileMailer $mailer)
	{
		$this->mailManager = $mailManager;
		$this->mailer = $mailer;
	}

	/**
	 * @dataProvider mailManagettest-send.ini
	 */
	public function testSend($email, $body)
	{
		$message = $this->mailManager->createMessage($body, array('variable' => 'Hello'))
			->addTo($email);

		/* Check returned object */
		Assert::true($message instanceof Mail\Message);
		$this->mailManager->send($message);

		/* Check temporary file */
		Assert::true(file_exists($this->mailer->getLastFile()));
		Assert::true(filesize($this->mailer->getLastFile()) > 0);
	}

	public function testParseSystemMail()
	{
		$message = $this->mailManager->createSystemMail(file_get_contents(__DIR__ . '/../template/system-mail.eml'));
		$this->mailManager->send($message);
		Assert::true(file_exists($this->mailer->getLastFile()));
		Assert::true(filesize($this->mailer->getLastFile()) > 0);
	}

}

/* @var $mailManager MailManager */
$mailManager = $container->getByType('h4kuna\MailManager\MailManager');

/* @var $mailer Mailer\FileMailer */
$mailer = $container->getService('mailManagerExtension.fileMailer');

(new MailManagerTest($mailManager, $mailer))->run();

