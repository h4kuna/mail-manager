<?php

namespace h4kuna\MailManager\Mailer;

use Nette\Mail;
use Nette\Utils;

/**
 * File Mailer - store mail to server uploads (file)
 * @todo https://github.com/romanmatyus/FileMailer
 * @author David Grudl
 * @author Milan Matejcek
 */
class FileMailer implements Mail\IMailer
{

	/** @var string */
	private $path;

	/** @var string */
	private $live;

	/** @var string */
	private $lastFile;

	/**
	 * @param $path
	 */
	public function __construct($path)
	{
		Utils\FileSystem::createDir($path);
		$this->path = realpath($path) . DIRECTORY_SEPARATOR;
	}

	public function setLive($live)
	{
		$this->live = $live;
		return $this;
	}

	public function getLastFile()
	{
		return $this->lastFile;
	}

	/**
	 * @param Mail\Message $mail
	 */
	public function send(Mail\Message $mail)
	{
		$this->autoremove();
		list($sec) = explode(' ', substr(microtime(), 2));
		$this->lastFile = $this->path . date('Y-m-d_H-i-s-') . $sec . '.eml';
		file_put_contents($this->lastFile, $mail->generateMessage());
	}

	private function autoremove()
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
