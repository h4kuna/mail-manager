<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail;

/**
 * @author Milan Matějček
 */
interface IMessageFactory
{

	/**
	 * @return Mail\Message
	 */
	function create();


	/**
	 * @return SystemMessage
	 */
	function createSystemMessage();
}
