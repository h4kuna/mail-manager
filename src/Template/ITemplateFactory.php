<?php

namespace h4kuna\MailManager\Template;

use Nette\Application\UI;

interface ITemplateFactory
{

	/** @return UI\ITemplate */
	function create();
}
