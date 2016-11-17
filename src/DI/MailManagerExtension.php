<?php

namespace h4kuna\MailManager\DI;

use Nette\DI\CompilerExtension;

class MailManagerExtension extends CompilerExtension
{

	public $defaults = [
		// mail manager
		'assetsDir' => NULL,
		// layout
		'templateDir' => NULL,
		'plainMacro' => '%file%-plain', // plain/%file% or plain-%file%
		// message
		'from' => NULL,
		'returnPath' => NULL,
		// file mailer
		'development' => '%debugMode%',
		'tempDir' => '%tempDir%/mails',
		'live' => '1 minute',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$this->defaults['plainMacro'] = str_replace('%', '=', $this->defaults['plainMacro']);
		$config = $this->getConfig($this->defaults);

		// message factory
		$builder->addDefinition($this->prefix('messageFactory'))
			->setClass('h4kuna\MailManager\Message\MessageFactory')
			->setArguments([$config['from'], $config['returnPath']])
			->setAutowired(FALSE);

		// template factory
		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'));
		$templateFactory->setClass('h4kuna\MailManager\Template\TemplateFactory')
			->setAutowired(FALSE);

		// mailer
		$mailer = '@nette.mailer';
		if ($config['development']) {
			$mailerBuilder = $builder->addDefinition($this->prefix('fileMailer'))
				->setClass('h4kuna\MailManager\Mailer\FileMailer')
				->setArguments([$config['tempDir']])
				->setAutowired(FALSE);

			if ($config['live'] !== NULL) {
				$mailerBuilder->addSetup('setLive', [$config['live']]);
			}
			$mailer = $this->prefix('@fileMailer');
		}

		// layout factory
		$builder->addDefinition($this->prefix('layoutFactory'))
			->setClass('h4kuna\MailManager\Template\LayoutFactory')
			->setArguments([$this->prefix('@templateFactory')])
			->addSetup('setTemplateDir', [$config['templateDir']])
			->addSetup('setPlainMacro', [$config['plainMacro']])
			->setAutowired(FALSE);

		// MailManager

		$builder->addDefinition($this->prefix('mailManager'))
			->setClass('h4kuna\MailManager\MailManager')
			->setArguments([$mailer, $this->prefix('@messageFactory'), $this->prefix('@layoutFactory')])
			->addSetup('setAssetsDir', [$config['assetsDir']]);

		return $builder;
	}

}
