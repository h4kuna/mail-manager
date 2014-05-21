<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail\Message;
use Nette\Object;

/**
 *
 * @author Milan Matějček
 */
class MessageFactory extends Object implements IMessageFactory {

    /** @var string */
    private $from;

    /** @var string */
    private $returnPath;

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setReturnPath($returnPath) {
        $this->returnPath = $returnPath;
    }

    public function create() {
        $message = new Message;
        if ($this->from) {
            $message->setFrom($this->from);
        }

        if ($this->returnPath) {
            $message->setReturnPath($this->returnPath);
        }
        return $message;
    }

}
