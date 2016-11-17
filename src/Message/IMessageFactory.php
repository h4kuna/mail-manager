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
	 * @param string $email
	 */
	function setFrom($email);

	/**
	 * @param string $email
	 */
	function setReturnPath($email);
}
