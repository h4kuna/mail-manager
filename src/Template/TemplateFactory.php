<?php declare(strict_types=1);

namespace h4kuna\MailManager\Template;

use Nette\Application;
use Nette\Bridges\ApplicationLatte;

class TemplateFactory implements ITemplateFactory
{

	/** @var Application\UI\ITemplateFactory */
	private $templateFactory;

	/** @var Application\LinkGenerator */
	private $linkGenerator;

	/** @var mixed[] */
	private $variables = [];


	public function __construct(
		Application\UI\ITemplateFactory $templateFactory,
		Application\LinkGenerator $linkGenerator
	)
	{
		$this->templateFactory = $templateFactory;
		$this->linkGenerator = $linkGenerator;
	}


	public function setVariables(array $variables): void
	{
		$this->variables = $variables;
	}


	public function create(): Application\UI\ITemplate
	{
		/** @var ApplicationLatte\Template $template */
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);
		foreach ($this->variables as $name => $value) {
			$template->{$name} = $value;
		}
		return $template;
	}

}
