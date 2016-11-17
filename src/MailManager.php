<?php

namespace h4kuna\MailManager;

use h4kuna\MailManager\Message,
	Nette\Application\UI,
	Nette\Mail;

class MailManager
{

	/** @var Mail\IMailer */
	private $mailer;

	/** @var Message\IMessageFactory */
	private $messageFactory;

	/** @var Template\LayoutFactory */
	private $layoutFactory;

	/** @var string */
	private $assetsDir;

	public function __construct(Mail\IMailer $mailer, Message\IMessageFactory $messageFactory, Template\LayoutFactory $layoutFactory)
	{
		$this->mailer = $mailer;
		$this->messageFactory = $messageFactory;
		$this->layoutFactory = $layoutFactory;
	}

	/** @var Layout */
	public function getLastTemplate()
	{
		return $this->layoutFactory->getLastLayout();
	}

	public function setAssetsDir($path)
	{
		$dir = realpath($path);
		if (!$dir) {
			throw new DirectoryNotFoundException($path);
		}
		$this->assetsDir = $dir;
	}

	/**
	 * @param string|UI\ITemplate $body content or filepath
	 * @return Mail\Message
	 */
	public function createMessage($body, array $data = [])
	{
		if ($body instanceof Template\Layout) {
			$layout = $body;
		} else {
			$layout = $this->layoutFactory->createHtml($body);
		}

		$message = $this->messageFactory->create();
		$layout->bindMessage($message, $data, $this->assetsDir);
		return $message;
	}

	public function getLayoutFactory()
	{
		return $this->layoutFactory;
	}

	/**
	 * Send email
	 */
	public function send(Mail\Message $message)
	{
		$this->mailer->send($message);
	}

	/**
	 * Plain message generated by function mail
	 * @param string $content
	 */
	public function createSystemMail($content)
	{
		$message = $this->messageFactory->createSystemMessage();
		$message->setBody($content);
		return $message;
	}

}
