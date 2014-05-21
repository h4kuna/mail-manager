<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail\Message;

/**
 *
 * @author Milan Matějček
 */
interface IMessageFactory {

    /**
     * @return Message
     */
    public function create();

    /**
     * 
     * @param string $email
     */
    public function setFrom($email);

    /**
     * 
     * @param string $email
     */
    public function setReturnPath($email);
}
