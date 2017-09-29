<?php

namespace h4kuna\MailManager\Template;

use Nette\Application;

/**
 * @author Milan Matějček
 */
class TemplateFactory implements ITemplateFactory
{

	/** @var Application\UI\ITemplateFactory */
	private $templateFactory;

	/** @var Application\LinkGenerator */
	private $linkGenerator;

	/** @var array */
	private $variables = [];


	public function __construct(Application\UI\ITemplateFactory $templateFactory, Application\LinkGenerator $linkGenerator)
	{
		$this->templateFactory = $templateFactory;
		$this->linkGenerator = $linkGenerator;
	}


	public function setVariables(array $variables)
	{
		$this->variables = $variables;
	}


	public function create()
	{
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);
		foreach ($this->variables as $name => $value) {
			$template->{$name} = $value;
		}
		return $template;
	}

}
