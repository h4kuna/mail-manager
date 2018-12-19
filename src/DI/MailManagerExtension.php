<?php

namespace h4kuna\MailManager\DI;

use h4kuna\MailManager;
use Nette\DI as NDI;

class MailManagerExtension extends NDI\CompilerExtension
{

	private $defaults = [
		// mail manager
		'assetsDir' => null,
		// layout
		'templateDir' => null,
		'plainMacro' => '{file}-plain', // plain/%file% or plain-%file%
		// template factory
		'globalVars' => [],
		// message
		'from' => null,
		'returnPath' => null,
		// file mailer
		'debugMode' => '%debugMode%',
		'tempDir' => '%tempDir%/mails',
		'live' => '1 minute',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		// message factory
		$builder->addDefinition($this->prefix('messageFactory'))
			->setFactory(MailManager\Message\MessageFactory::class)
			->setArguments([$config['from'], $config['returnPath']])
			->setAutowired(false);

		// template factory
		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'));
		$templateFactory->setFactory(MailManager\Template\TemplateFactory::class)
			->addSetup('setVariables', [$config['globalVars']])
			->setAutowired(false);

		// mailer
		$mailer = '@nette.mailer';
		if ($config['debugMode']) {
			$mailerBuilder = $builder->addDefinition($this->prefix('fileMailer'))
				->setFactory(MailManager\Mailer\FileMailer::class)
				->setArguments([$config['tempDir']])
				->setAutowired(false);

			if ($config['live'] !== null) {
				$mailerBuilder->addSetup('setLive', [$config['live']]);
			}
			$mailer = $this->prefix('@fileMailer');
		}

		// layout factory
		$builder->addDefinition($this->prefix('layoutFactory'))
			->setFactory(MailManager\Template\LayoutFactory::class)
			->setArguments([$this->prefix('@templateFactory')])
			->addSetup('setTemplateDir', [$config['templateDir']])
			->addSetup('setPlainMacro', [$config['plainMacro']])
			->setAutowired(false);

		// MailManager
		$mailManager = $builder->addDefinition($this->prefix('mailManager'))
			->setFactory(MailManager\MailManager::class)
			->setArguments([$mailer, $this->prefix('@messageFactory'), $this->prefix('@layoutFactory')]);

		if ($config['assetsDir'] !== NULL) {
			$mailManager->addSetup('setAssetsDir', [$config['assetsDir']]);
		}

		return $builder;
	}

}
