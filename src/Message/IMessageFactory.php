<?php declare(strict_types=1);

namespace h4kuna\MailManager\Message;

use Nette\Mail;

interface IMessageFactory
{

	function create(): Mail\Message;


	function createSystemMessage(): SystemMessage;

}
