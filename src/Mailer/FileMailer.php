<?php declare(strict_types=1);

namespace h4kuna\MailManager\Mailer;

use Nette\Mail;
use Nette\Utils;

/**
 * File Mailer - store mail to server uploads (file)
 * @todo https://github.com/romanmatyus/FileMailer
 * @author David Grudl
 */
class FileMailer implements Mail\IMailer
{

	/** @var string */
	private $path;

	/** @var string|null */
	private $live;

	/** @var string */
	private $lastFile;


	public function __construct(string $path)
	{
		Utils\FileSystem::createDir($path);
		$this->path = realpath($path) . DIRECTORY_SEPARATOR;
	}


	public function setLive(string $live): void
	{
		$this->live = $live;
	}


	public function getLastFile(): string
	{
		return $this->lastFile;
	}


	public function send(Mail\Message $mail): void
	{
		$this->autoremove();
		list($sec) = explode(' ', substr(microtime(), 2));
		$this->lastFile = $this->path . date('Y-m-d_H-i-s-') . $sec . '.eml';
		file_put_contents($this->lastFile, $mail->generateMessage());
	}


	private function autoremove(): void
	{
		if (!$this->live) {
			return;
		}
		$finder = Utils\Finder::findFiles('*.eml');
		if (is_string($this->live)) {
			$finder->date('<=', "- {$this->live}");
		}

		foreach ($finder->in($this->path) as $file) {
			@unlink($file->getPathname());
		}
		$this->live = null; // clear temporary onetime per execution.
	}

}
