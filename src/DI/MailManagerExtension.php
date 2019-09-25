<?php declare(strict_types=1);

namespace h4kuna\MailManager\DI;

use h4kuna\MailManager;
use Nette\DI as NDI;
use Nette\Mail;
use Nette\Schema;

class MailManagerExtension extends NDI\CompilerExtension
{

	/** @var bool */
	private $debugMode;


	public function __construct(bool $debugMode = false)
	{
		$this->debugMode = $debugMode;
	}


	public function loadConfiguration()
	{
		$this->buildMessageFactory((string) $this->config->from, (string) $this->config->returnPath);

		$this->buildTemplateFactory($this->config->globalVars);

		$mailer = $this->buildMailer($this->config->debugMode, $this->config->tempDir, $this->config->live);

		$this->buildLayoutFactory((string) $this->config->templateDir, $this->config->plainMacro);

		$this->buildMailManager($mailer, (string) $this->config->assetsDir);
	}


	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::structure([
			// mail manager
			'assetsDir' => Schema\Expect::string(''),

			// layout
			'templateDir' => Schema\Expect::string(''),
			'plainMacro' => Schema\Expect::string('{file}-plain'), // plain/%file% or plain-%file%

			// template factory
			'globalVars' => Schema\Expect::array([]),

			// message
			'from' => Schema\Expect::string(''),
			'returnPath' => Schema\Expect::string(''),

			// file mailer
			'debugMode' => Schema\Expect::bool($this->debugMode),
			'tempDir' => Schema\Expect::string('/tmp'),
			'live' => Schema\Expect::string('1 minute')->nullable(),
		]);
	}


	private function buildMessageFactory(string $from, string $returnPath): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('messageFactory'))
			->setFactory(MailManager\Message\MessageFactory::class)
			->setArguments([$from, $returnPath])
			->setAutowired(false);
	}


	private function buildTemplateFactory(array $globalVars): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('templateFactory'))
			->setFactory(MailManager\Template\TemplateFactory::class)
			->addSetup('setVariables', [$globalVars])
			->setAutowired(false);
	}


	private function buildLayoutFactory(string $templateDir, string $plainMacro): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('layoutFactory'))
			->setFactory(MailManager\Template\LayoutFactory::class)
			->setArguments([$this->prefix('@templateFactory')])
			->addSetup('setTemplateDir', [$templateDir])
			->addSetup('setPlainMacro', [$plainMacro])
			->setAutowired(false);
	}


	private function buildMailer(bool $debugMode, string $tempDir, ?string $live): string
	{
		if (!$debugMode) {
			try {
				$definition = $this->getContainerBuilder()->getDefinitionByType(Mail\IMailer::class);
			} catch (NDI\MissingServiceException $e) {
				return '';
			}
			return '@' . $definition->getName();
		}
		$mailerBuilder = $this->getContainerBuilder()
			->addDefinition($this->prefix('fileMailer'))
			->setFactory(MailManager\Mailer\FileMailer::class)
			->setArguments([$tempDir])
			->setAutowired(false);

		if ($live !== null) {
			$mailerBuilder->addSetup('setLive', [$live]);
		}
		return $this->prefix('@fileMailer');
	}


	private function buildMailManager(string $mailer, string $assetsDir): void
	{
		$mailManager = $this->getContainerBuilder()
			->addDefinition($this->prefix('mailManager'))
			->setFactory(MailManager\MailManager::class)
			->setArguments([$mailer, $this->prefix('@messageFactory'), $this->prefix('@layoutFactory')]);

		if ($assetsDir !== '') {
			$mailManager->addSetup('setAssetsDir', [$assetsDir]);
		}
	}

}
