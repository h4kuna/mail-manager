<?php

namespace h4kuna\MailManager;

use h4kuna\MailManager\Message\IMessageFactory;
use h4kuna\MailManager\Message\SystemMessage;
use h4kuna\MailManager\Template\ITemplateFactory;
use Nette\DirectoryNotFoundException;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Object;
use Nette\Templating\FileTemplate;
use Nette\Application\UI\ITemplate;
use ArrayAccess;

class MailManager extends Object implements ArrayAccess
{

    /** @var IMailer */
    private $mailer;

    /** @var Message */
    private $message;

    /** @var IMessageFactory */
    private $messageFactory;

    /** @var ITemplateFactory */
    private $templateFactory;

    /** @var bool */
    private $html = TRUE;

    /** @var string */
    private $mailDir;

    /** @var string */
    private $imageDir;

    /** @var \Latte\Template[] */
    private $templates = array();

    /** @var \Latte\Template */
    private $lastTemplate;

    /** @var array */
    public $onCreateMessage;

    public function __construct(IMailer $mailer, ITemplateFactory $templateFactory, IMessageFactory $messageFactory)
    {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
        $this->templateFactory = $templateFactory;
    }

    public function getLastTemplate()
    {
        return $this->lastTemplate;
    }

    public function setTemplateDir($path)
    {
        $this->mailDir = realpath($path);
        return $this;
    }

    public function setImageDir($path)
    {
        $dir = realpath($path);
        if (!$dir) {
            throw new DirectoryNotFoundException($path);
        }
        $this->imageDir = $dir;
        return $this;
    }

    public function onHtml()
    {
        $this->html = TRUE;
        return $this;
    }

    public function offHtml()
    {
        $this->html = FALSE;
        return $this;
    }

    /** @return Message */
    private function _createMessage()
    {
        return $this->message = $this->messageFactory->create();
    }

    /**
     * @param string|array $to email or email <name>
     * @param string|ITemplate $body content or filepath
     * @return Message
     */
    public function createMessage($to, $body, array $data = array())
    {
        $this->_createMessage();
        $name = NULL;
        if(is_array($to)) {
            list($to, $name) = $to;
        }
        $this->message->addTo($to, $name);
        if ($body instanceof ITemplate) {
            $template = $body;
        } else {
            $template = $this->createTemplate($body, $data);
        }

        $this->onCreateMessage($this->message, $template, $this->messageFactory);

        if ($this->html) {
            $this->message->setHtmlBody($template, $this->imageDir);
        } else {
            $this->message->setBody($template);
        }

        return $this->message;
    }

    /**
     *
     * @param string $body
     * @param array $data
     * @return FileTemplate|string
     */
    public function createTemplate($body, array $data = array())
    {
        $filePath = $this->checkFile($body);
        if (!$filePath) {
            return $body;
        }

        if ($this->issetTemplate($filePath)) {
            $template = $this->templates[$filePath];
            return $this->bindTemplate($template, $data);
        }

        $template = $this->loadTemplate($filePath);
        $this->bindTemplate($template, $data);
        $template->action = $body;
        $template->imageDir = '';

        return $this->lastTemplate = $this->templates[$filePath] = $template;
    }

    /**
     *
     * @param string $filePath
     * @return string|boolean
     */
    private function checkFile($filePath)
    {
        $file = $this->mailDir . DIRECTORY_SEPARATOR . $filePath . '.latte';
        if ($this->mailDir && is_file($file)) {
            return $file;
        }

        if (is_file($filePath)) {
            return $filePath;
        }
        return FALSE;
    }

    /**
     *
     * @param string $filePath
     * @return bool
     */
    private function issetTemplate($filePath)
    {
        return isset($this->templates[$filePath]);
    }

    /**
     * Add variable to template
     *
     * @param ITemplate $template
     * @param array $data
     * @return ITemplate
     */
    private function bindTemplate(ITemplate $template, array $data)
    {
        foreach ($data as $key => $value) {
            $template->{$key} = $value;
        }
        return $template;
    }

    /**
     *
     * @param string $file
     * @return ITemplate
     */
    private function loadTemplate($file)
    {
        $this->html = strpos($file, 'plain') === FALSE;
        $template = $this->templateFactory->create();
        $template->setFile($file);
        return $template;
    }

    /**
     * Send email
     */
    public function send()
    {
        $this->mailer->send($this->message);
    }

    /** @return Message */
    public function getMessage()
    {
        $this->message || $this->_createMessage();
        return $this->message;
    }

    /**
     * Plain message generated by function mail
     *
     * @param string $content
     */
    public function createSystemMail($content)
    {
        $message = new SystemMessage();
        $msg = $this->_createMessage();
        $message->setFrom($msg->getFrom());
        $message->setReturnPath($msg->getReturnPath());
        $message->setBody($content);
        return $this->message = $message;
    }

    public function offsetExists($offset)
    {
        return $this->issetTemplate($this->checkFile($offset));
    }

    public function offsetGet($offset)
    {
        return $this->templates[$this->checkFile($offset)];
    }

    public function offsetSet($offset, $value)
    {
        return $this->templates[$this->checkFile($offset)] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->templates[$this->checkFile($offset)]);
    }

}
