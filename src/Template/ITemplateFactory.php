<?php

namespace h4kuna\MailManager\Template;

use Nette\Application\UI;

/**
 * @author Milan Matejcek
 */
interface ITemplateFactory
{

	/** @var UI\ITemplate */
	function create();
}
