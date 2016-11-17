<?php

namespace h4kuna\MailManager\DI;

use Nette\DI\CompilerExtension;

class MailManagerExtension extends CompilerExtension
{

	public $defaults = [
		'imageDir' => NULL,
		'templateDir' => NULL,
		'tempDir' => '%tempDir%/mails',
		'from' => NULL,
		'returnPath' => NULL,
		'templateFactory' => 'h4kuna\MailManager\Template\TemplateFactory',
		'messageFactory' => 'h4kuna\MailManager\Message\MessageFactory',
		'development' => '%debugMode%',
		'live' => NULL
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		// message factory
		$builder->addDefinition($this->prefix('messageFactory'))
			->setClass($config['messageFactory'])
			->setAutowired(FALSE)
			->addSetup('setFrom', [$config['from']])
			->addSetup('setReturnPath', [$config['returnPath']]);

		// storage factory
		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'));
		$templateFactory->setClass($config['templateFactory'])
			->setAutowired(FALSE);

		// mailer
		if ($config['development']) {
			$builder->removeDefinition('nette.mailer');
			$mailerBuilder = $builder->addDefinition('nette.mailer')
				->setClass('h4kuna\MailManager\Mailer\FileMailer')
				->setArguments([$config['tempDir']]);

			if ($config['live'] !== NULL) {
				$mailerBuilder->addSetup('setLive', [$config['live']]);
			}
		}

		// MailManager
		$builder->addDefinition($this->prefix('mailManager'))
			->setClass('h4kuna\MailManager\MailManager')
			->setArguments(['@nette.mailer', $this->prefix('@templateFactory'), $this->prefix('@messageFactory')])
			->addSetup('setImageDir', [$config['imageDir']])
			->addSetup('setTemplateDir', [$config['templateDir']]);

		return $builder;
	}

}
