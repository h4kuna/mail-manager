<?php

namespace h4kuna\MailManager\Template;

use Nette\Mail,
	Nette\Application\UI;

class Layout
{

	/** @var string|object */
	private $html;

	/** @var string|object */
	private $plain;

	public function __construct($plain = NULL)
	{
		$this->setPlain($plain);
	}

	public function setHtml($html)
	{
		$this->html = $html;
	}

	public function setPlain($plain)
	{
		$this->plain = $plain;
	}

	public function bindMessage(Mail\Message $message, array $data = [], $assetsDir = NULL)
	{
		if ($this->plain) {
			if ($this->plain instanceof UI\ITemplate) {
				$this->bindNetteTemplate($this->plain, $data);
			}
			$message->setBody($this->plain);
		}
		if ($this->html) {
			if ($this->html instanceof UI\ITemplate) {
				$this->bindNetteTemplate($this->html, $data);
			}
			$message->setHtmlBody($this->html, $assetsDir);
		}
	}

	/**
	 * Add variable to template
	 * @param UI\ITemplate $template
	 * @param array $data
	 */
	private function bindNetteTemplate(UI\ITemplate $template, array $data)
	{
		foreach ($data as $key => $value) {
			$template->{$key} = $value;
		}
	}

}
