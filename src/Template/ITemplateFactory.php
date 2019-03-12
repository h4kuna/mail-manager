<?php declare(strict_types=1);

namespace h4kuna\MailManager\Template;

use Nette\Application\UI;

interface ITemplateFactory
{

	function create(): UI\ITemplate;

}
