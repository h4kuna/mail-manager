<?php declare(strict_types=1);

namespace h4kuna\MailManager\Template;

use Nette\Application\UI\ITemplate;

class LayoutFactory
{

	/** @var ITemplateFactory */
	private $templateFactory;

	/** @var string */
	private $mailDir;

	/** @var Layout */
	private $lastLayout;

	/** @var string */
	private $plainMacro;

	/** @var Layout[] */
	private $netteLayouts;


	public function __construct(ITemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}


	public function setTemplateDir(string $path): void
	{
		$this->mailDir = $path;
	}


	public function setPlainMacro(string $plainMacro): void
	{
		$this->plainMacro = $plainMacro;
	}


	public function getLastLayout(): Layout
	{
		return $this->lastLayout;
	}


	public function createHtml($body): Layout
	{
		return $this->createBody($body, true);
	}


	public function createPlainText($body): Layout
	{
		return $this->createBody($body, false);
	}


	private function createBody($body, bool $html): Layout
	{
		$file = is_string($body) ? $this->checkFile($body) : null;

		if ($file === null) {
			$layout = $this->createLayoutClass();
			if ($html) {
				$layout->setHtml($body);
			} else {
				$layout->setPlain($body);
			}
		} else {
			$layout = $this->createLayout($file, $body);
		}
		return $this->lastLayout = $layout;
	}


	private function createLayout(string $file, string $fileName): Layout
	{
		if (isset($this->netteLayouts[$file])) {
			return $this->netteLayouts[$file];
		}
		$layout = $this->createLayoutClass();
		$layout->setHtml($this->createNetteTemplate($file));

		$plain = str_replace('{file}', $fileName, $this->plainMacro);
		$plainFile = $this->checkFile($plain);
		if ($plainFile) {
			$layout->setPlain($this->createNetteTemplate($plainFile));
		}

		return $layout;
	}


	private function createLayoutClass(): Layout
	{
		return new Layout;
	}


	private function createNetteTemplate(string $file): ITemplate
	{
		$template = $this->templateFactory->create();
		$template->setFile($file);
		return $template;
	}


	private function checkFile(string $filePath): ?string
	{
		$file = $this->mailDir . DIRECTORY_SEPARATOR . $filePath . '.latte';
		if ($this->mailDir && is_file($file)) {
			return $file;
		} elseif (is_file($filePath)) {
			return $filePath;
		}
		return null;
	}

}
