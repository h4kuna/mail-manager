<?php

namespace h4kuna\MailManager\Template;

use Nette\Templating\ITemplate;

/**
 * @author Milan Matejcek
 */
interface ITemplateFactory
{

	/** @var ITemplate */
	function create();
}
