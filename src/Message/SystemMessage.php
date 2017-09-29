<?php

namespace h4kuna\MailManager\Message;

use Nette\Mail;

/**
 * Send system mail
 * @author Milan Matejcek
 */
class SystemMessage extends Mail\Message
{

	public function setBody($body)
	{
		$find = null;
		preg_match_all('/^([A-Z].*?): (.*)$/m', $body, $find);
		if ($find[0]) {
			foreach ($find[1] as $k => $header) {
				$method = 'add' . ucfirst($header);
				if (method_exists($this, $method)) {
					$this->{$method}($find[2][$k]);
				} else {
					$this->setHeader($header, $find[2][$k]);
				}
			}

			preg_match("/\n{2,}(.*)$/s", $body, $find);

			$body = $find[1];
		}
		return parent::setBody($body);
	}


	public function setFrom($email, $name = null)
	{
		list($email, $name) = $this->toString($email, $name);
		return parent::setFrom($email, $name);
	}


	public function setReturnPath($email)
	{
		list($email) = $this->toString($email);
		return parent::setReturnPath($email);
	}


	private function toString($email, $name = null)
	{
		if (is_array($email)) {
			return [key($email), current($email)];
		}
		return [$email, $name];
	}

}
