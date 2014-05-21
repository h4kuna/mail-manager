<?php

namespace h4kuna\MailManager\Mailer;

use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Utils\Finder;

/**
 * File Mailer - store mail to server uploads (file)
 *
 * @todo https://github.com/romanmatyus/FileMailer
 * @author David Grudl
 * @author Milan Matejcek
 *
 */
class FileMailer implements IMailer {

    /** @var string */
    private $path;

    /** @var string */
    private $extension = 'eml';

    /** @var string */
    private $live = '1 minute';

    /**
     * @param $path
     */
    public function __construct($path) {
        @mkdir($path, 0777, TRUE);
        $this->path = realpath($path) . DIRECTORY_SEPARATOR;
    }

    public function setExtension($extension) {
        $this->extension = $extension;
        return $this;
    }

    public function setLive($live) {
        $this->live = $live;
        return $this;
    }

    /**
     * @param Message $mail
     */
    public function send(Message $mail) {
        $this->autoremove();
        list($sec) = explode(' ', substr(microtime(), 2));
        file_put_contents($this->path . date('Y-m-d_H-i-s-') . $sec . '.' . $this->extension, $mail->generateMessage());
    }

    /** @return void */
    private function autoremove() {
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
    }

}
