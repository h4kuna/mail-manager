<?php declare(strict_types=1);

namespace h4kuna\MailManager;

use h4kuna\MailManager\Exceptions\DirectoryNotFound;
use h4kuna\MailManager\Message;
use Nette\Application\UI\ITemplate;
use Nette\Mail;

class MailManager
{

	/** @var Mail\IMailer */
	private $mailer;

	/** @var Message\IMessageFactory */
	private $messageFactory;

	/** @var Template\LayoutFactory */
	private $layoutFactory;

	/** @var string */
	private $assetsDir = '';


	public function __construct(
		Mail\IMailer $mailer,
		Message\IMessageFactory $messageFactory,
		Template\LayoutFactory $layoutFactory
	)
	{
		$this->mailer = $mailer;
		$this->messageFactory = $messageFactory;
		$this->layoutFactory = $layoutFactory;
	}


	public function getLastTemplate(): Template\Layout
	{
		return $this->layoutFactory->getLastLayout();
	}


	public function setAssetsDir(string $path): void
	{
		$dir = realpath($path);
		if ($dir === false) {
			throw new DirectoryNotFound($path);
		}
		$this->assetsDir = $dir;
	}


	/**
	 * @param string|ITemplate|Template\Layout $body content or filepath
	 * @param mixed[] $data
	 */
	public function createMessage($body, array $data = []): Mail\Message
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


	public function getLastLayout(): Template\Layout
	{
		return $this->layoutFactory->getLastLayout();
	}


	public function send(Mail\Message $message): void
	{
		$this->mailer->send($message);
	}


	/**
	 * Plain message generated by function mail
	 */
	public function createSystemMail(string $content): Message\SystemMessage
	{
		$message = $this->messageFactory->createSystemMessage();
		$message->setBody($content);
		return $message;
	}

}
