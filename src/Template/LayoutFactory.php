<?php

namespace h4kuna\MailManager\Template;

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

	public function setTemplateDir($path)
	{
		$this->mailDir = $path;
		return $this;
	}

	public function setPlainMacro($plainMacro)
	{
		$this->plainMacro = $plainMacro;
	}

	/** @return Layout */
	public function getLastLayout()
	{
		return $this->lastLayout;
	}

	/**
	 * @param string $body
	 * @return Layout
	 */
	public function createHtml($body)
	{
		return $this->createBody($body, true);
	}

	/**
	 * @param string $body
	 * @return Layout
	 */
	public function createPlainText($body)
	{
		return $this->createBody($body, false);
	}

	/**
	 * @param string $body
	 * @param bool $html
	 * @return Layout
	 */
	private function createBody($body, $html)
	{
		$file = $this->checkFile($body);
		if ($file) {
			$layout = $this->createLayout($file, $body);
		} else {
			$layout = $this->createLayoutClass();
			if ($html) {
				$layout->setHtml($body);
			} else {
				$layout->setPlain($body);
			}
		}
		return $this->lastLayout = $layout;
	}

	private function createLayout($file, $fileName)
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

	private function createLayoutClass()
	{
		return new Layout;
	}

	private function createNetteTemplate($file)
	{
		$template = $this->templateFactory->create();
		$template->setFile($file);
		return $template;
	}

	/**
	 * @param string $filePath
	 * @return string|boolean
	 */
	private function checkFile($filePath)
	{
		$file = $this->mailDir . DIRECTORY_SEPARATOR . $filePath . '.latte';
		if ($this->mailDir && is_file($file)) {
			return $file;
		} elseif (is_file($filePath)) {
			return $filePath;
		}
		return false;
	}

}
