<?php declare(strict_types=1);

namespace h4kuna\MailManager\Message;

use Nette\Mail;

class MessageFactory implements IMessageFactory
{

	/** @var string */
	private $from;

	/** @var string */
	private $returnPath;


	public function __construct(string $from, string $returnPath)
	{
		$this->from = $from;
		$this->returnPath = $returnPath;
	}


	public function create(): Mail\Message
	{
		$message = new Mail\Message;
		$this->bindFrom($message);
		$this->bindReturnPath($message);
		return $message;
	}


	public function createSystemMessage(): SystemMessage
	{
		$message = new SystemMessage;
		$this->bindFrom($message);
		$this->bindReturnPath($message);
		return $message;
	}


	private function bindFrom(Mail\Message $message): void
	{
		if ($this->from !== '') {
			$message->setFrom($this->from);
		}
	}


	private function bindReturnPath(Mail\Message $message): void
	{
		if ($this->returnPath !== '') {
			$message->setReturnPath($this->returnPath);
		}
	}

}
