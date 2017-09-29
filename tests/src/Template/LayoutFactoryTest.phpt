<?php

namespace h4kuna\MailManager\Template;

use Tester\Assert;
use Salamium\Testinium\File;

$container = require __DIR__ . '/../../bootstrap.php';

class LayoutFactoryTest extends \Tester\TestCase
{

	/** @var LayoutFactory */
	private $layoutFactory;

	public function __construct(LayoutFactory $layoutFactory)
	{
		$this->layoutFactory = $layoutFactory;
	}

	public function testPlainText()
	{
		$layout = $this->layoutFactory->createPlainText('ahoj');
		Assert::same('ahoj', $this->getBody($layout));
	}

	public function testPlainTextByStringableObject()
	{
		$layout = $this->layoutFactory->createPlainText(new TextObjext('ahoj'));
		Assert::same('ahoj', $this->getBody($layout));
	}

	public function testPlainTextByFile()
	{
		$layout = $this->layoutFactory->createPlainText('test-plain');
		Assert::same('ahoj', $this->getBody($layout, ['variable' => 'ahoj']));
	}

	public function testHtmlText()
	{
		$layout = $this->layoutFactory->createHtml('<i>ahoj</i>');
		$message = new \Nette\Mail\Message;
		$layout->bindMessage($message);
		Assert::same('ahoj', $message->getBody());
		Assert::same('<i>ahoj</i>', $message->getHtmlBody());
	}

	public function testHtmlPlainByFile()
	{
		$layout = $this->layoutFactory->createHtml('test');
		$message = new \Nette\Mail\Message;
		$layout->bindMessage($message, ['variable' => 'ahoj']);

		Assert::contains('Here I am.', trim($message->getHtmlBody()));
		Assert::contains('ahoj', trim($message->getBody()));

		// File::save('layout-html-1.html', $message->getHtmlBody());
		Assert::same(File::load('layout-html-1.html'), $message->getHtmlBody());
	}

	public function testHtmlByFile()
	{
		$layout = $this->layoutFactory->createPlainText('test-2');
		$message = new \Nette\Mail\Message;
		$layout->bindMessage($message, ['variable' => 'ahoj']);

		// File::save('layout-plain-2.txt', trim($message->getBody()));
		Assert::same(File::load('layout-plain-2.txt'), $message->getBody());

		// File::save('layout-html-2.html', $message->getHtmlBody());
		Assert::same(File::load('layout-html-2.html'), $message->getHtmlBody());
	}

	private function getBody(Layout $layout, array $data = [])
	{
		$message = new \Nette\Mail\Message;
		$layout->bindMessage($message, $data);
		return (string) $message->getBody();
	}

}

class TextObjext
{

	private $text;

	public function __construct($text)
	{
		$this->text = $text;
	}

	public function __toString()
	{
		return (string) $this->text;
	}

}

$layoutFactory = $container->getService('mailManager.layoutFactory');

(new LayoutFactoryTest($layoutFactory))->run();
