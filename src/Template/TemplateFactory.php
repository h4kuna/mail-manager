<?php

namespace h4kuna\MailManager\Template;

use Nette\DI\Container;

/**
 *
 * @author Milan Matějček
 */
class TemplateFactory implements ITemplateFactory
{

	/** @var Container */
	private $container;

	function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function create()
	{
		return $this->getTemplateFactory()->createTemplate($this->getPresenter());
	}

	private function getPresenter()
	{
		$presenter = $this->container->getService('application')->getPresenter();
		if ($presenter) {
			return $presenter;
		}
		return new FakeControl;
	}

	/** @return \Nette\Bridges\ApplicationLatte\TemplateFactory */
	private function getTemplateFactory()
	{
		return $this->container->createService('nette.templateFactory');
	}

}
