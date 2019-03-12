<?php declare(strict_types=1);

namespace h4kuna\MailManager\Message;

use Nette\Mail;

/**
 * Send system mail
 */
class SystemMessage extends Mail\Message
{

	public function setBody(string $body)
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

}
