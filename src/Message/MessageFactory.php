<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail;

/**
 * @author Milan Matějček
 */
class MessageFactory implements IMessageFactory
{

	/** @var string */
	private $from;

	/** @var string */
	private $returnPath;

	public function setFrom($from)
	{
		$this->from = $from;
	}

	public function setReturnPath($returnPath)
	{
		$this->returnPath = $returnPath;
	}

	/**
	 * @return Mail\Message
	 */
	public function create()
	{
		$message = new Mail\Message;
		if ($this->from) {
			$message->setFrom($this->from);
		}

		if ($this->returnPath) {
			$message->setReturnPath($this->returnPath);
		}
		return $message;
	}

}
