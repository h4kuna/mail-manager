<?php declare(strict_types=1);

namespace h4kuna\MailManager\DI;

use h4kuna\MailManager;
use Nette\DI as NDI;

class MailManagerExtension extends NDI\CompilerExtension
{

	private $defaults = [
		// mail manager
		'assetsDir' => '',
		// layout
		'templateDir' => '',
		'plainMacro' => '{file}-plain', // plain/%file% or plain-%file%
		// template factory
		'globalVars' => [],
		// message
		'from' => '',
		'returnPath' => '',
		// file mailer
		'debugMode' => false,
		'tempDir' => '/tmp',
		'live' => '1 minute',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$this->buildMessageFactory($builder, (string) $config['from'], (string) $config['returnPath']);

		$this->buildTemplateFactory($builder, $config['globalVars']);

		$mailer = $this->buildMailer($builder, $config['debugMode'], $config['tempDir'], $config['live']);

		$this->buildLayoutFactory($builder, (string) $config['templateDir'], $config['plainMacro']);

		$this->buildMailManager($builder, $mailer, (string) $config['assetsDir']);
	}


	private function buildMessageFactory(NDI\ContainerBuilder $builder, string $from, string $returnPath): void
	{
		$builder->addDefinition($this->prefix('messageFactory'))
			->setFactory(MailManager\Message\MessageFactory::class)
			->setArguments([$from, $returnPath])
			->setAutowired(false);
	}


	private function buildTemplateFactory(NDI\ContainerBuilder $builder, array $globalVars): void
	{
		$builder->addDefinition($this->prefix('templateFactory'))
			->setFactory(MailManager\Template\TemplateFactory::class)
			->addSetup('setVariables', [$globalVars])
			->setAutowired(false);
	}


	private function buildLayoutFactory(NDI\ContainerBuilder $builder, $templateDir, $plainMacro): void
	{
		$builder->addDefinition($this->prefix('layoutFactory'))
			->setFactory(MailManager\Template\LayoutFactory::class)
			->setArguments([$this->prefix('@templateFactory')])
			->addSetup('setTemplateDir', [$templateDir])
			->addSetup('setPlainMacro', [$plainMacro])
			->setAutowired(false);
	}


	/**
	 * @param NDI\ContainerBuilder $builder
	 * @param bool $debugMode
	 * @param string $tempDir
	 * @param string|null $live
	 * @return string
	 */
	private function buildMailer(NDI\ContainerBuilder $builder, bool $debugMode, string $tempDir, ?string $live): string
	{
		if (!$debugMode) {
			return '@nette.mailer';
		}
		$mailerBuilder = $builder->addDefinition($this->prefix('fileMailer'))
			->setFactory(MailManager\Mailer\FileMailer::class)
			->setArguments([$tempDir])
			->setAutowired(false);

		if ($live !== null) {
			$mailerBuilder->addSetup('setLive', [$live]);
		}
		return $this->prefix('@fileMailer');
	}


	private function buildMailManager(NDI\ContainerBuilder $builder, string $mailer, string $assetsDir): void
	{
		$mailManager = $builder->addDefinition($this->prefix('mailManager'))
			->setFactory(MailManager\MailManager::class)
			->setArguments([$mailer, $this->prefix('@messageFactory'), $this->prefix('@layoutFactory')]);

		if ($assetsDir !== '') {
			$mailManager->addSetup('setAssetsDir', [$assetsDir]);
		}
	}

}
