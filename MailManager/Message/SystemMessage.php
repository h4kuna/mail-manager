<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail\Message;

/**
 * Send system mail
 *
 * @author Milan Matejcek
 */
class SystemMessage extends Message {

    public function setBody($body) {
        $find = NULL;
        preg_match_all('/^([A-Z].*?): (.*)$/m', $body, $find);
        if ($find[0]) {
            foreach ($find[1] as $k => $header) {
                $this->setHeader($header, $find[2][$k]);
            }

            preg_match("/\n{2,}(.*)$/s", $body, $find);

            $body = $find[1];
        }
        return parent::setBody($body);
    }

    public function setFrom($email, $name = NULL) {
        list($email, $name) = $this->toString($email, $name);
        return parent::setFrom($email, $name);
    }

    public function setReturnPath($email) {
        list($email) = $this->toString($email);
        return parent::setReturnPath($email);
    }

    private function toString($email, $name = NULL) {
        if (is_array($email)) {
            return array(key($email), current($email));
        }
        return array($email, $name);
    }

}
