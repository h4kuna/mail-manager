<?php declare(strict_types=1);

namespace h4kuna\MailManager\Template;

use Nette\Application\UI;
use Nette\Mail;

class Layout
{

	/** @var string|UI\ITemplate|null */
	private $html;

	/** @var string|UI\ITemplate|null */
	private $plain;


	public function __construct($plain = null)
	{
		$this->setPlain($plain);
	}


	/**
	 * @param string|UI\ITemplate|null $html
	 */
	public function setHtml($html): void
	{
		$this->html = $html;
	}


	/**
	 * @param string|UI\ITemplate|null $plain
	 */
	public function setPlain($plain): void
	{
		$this->plain = $plain;
	}


	public function bindMessage(Mail\Message $message, array $data = [], string $assetsDir = ''): void
	{
		if (self::isNotEmpty($this->plain)) {
			if ($this->plain instanceof UI\ITemplate) {
				self::bindNetteTemplate($this->plain, $data);
			}
			$message->setBody((string) $this->plain);
		}
		if (self::isNotEmpty($this->html)) {
			if ($this->html instanceof UI\ITemplate) {
				self::bindNetteTemplate($this->html, $data);
			}
			$message->setHtmlBody((string) $this->html, $assetsDir);
		}
	}


	/**
	 * Add variable to template
	 */
	private static function bindNetteTemplate(UI\ITemplate $template, array $data): void
	{
		foreach ($data as $key => $value) {
			$template->{$key} = $value;
		}
	}


	private static function isNotEmpty($variable): bool
	{
		return $variable !== null && $variable !== '';
	}

}
