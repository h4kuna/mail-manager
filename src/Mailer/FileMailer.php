<?php

namespace h4kuna\MailManager\Mailer;

use Nette\Mail\IMailer,
    Nette\Mail\Message,
    Nette\Utils\FileSystem,
    Nette\Utils\Finder;

/**
 * File Mailer - store mail to server uploads (file)
 *
 * @todo https://github.com/romanmatyus/FileMailer
 * @author David Grudl
 * @author Milan Matejcek
 *
 */
class FileMailer implements IMailer
{

    /** @var string */
    private $path;

    /** @var string */
    private $extension = 'eml';

    /** @var string */
    private $live = '1 minute';

    /** @var string */
    private $lastFile;

    /**
     * @param $path
     */
    public function __construct($path)
    {
        FileSystem::createDir($path);
        $this->path = realpath($path) . DIRECTORY_SEPARATOR;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
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
     * @param Message $mail
     */
    public function send(Message $mail)
    {
        $this->autoremove();
        list($sec) = explode(' ', substr(microtime(), 2));
        $this->lastFile = $this->path . date('Y-m-d_H-i-s-') . $sec . '.' . $this->extension;
        file_put_contents($this->lastFile, $mail->generateMessage());
    }

    /** @return void */
    private function autoremove()
    {
        if (!$this->live) {
            return;
        }
        $finder = Finder::findFiles('*.' . $this->extension);
        if (is_string($this->live)) {
            $finder->date('<=', "- {$this->live}");
        }

        foreach ($finder->in($this->path) as $file) {
            @unlink($file->getPathname());
        }
        $this->live = NULL; // clear temporary onetime per execution.
    }

}
