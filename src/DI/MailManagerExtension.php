<?php

namespace h4kuna\MailManager\DI;

use Nette\DI\CompilerExtension;

class MailManagerExtension extends CompilerExtension
{

    public $defaults = array(
        'imageDir' => NULL,
        'templateDir' => NULL,
        'tempDir' => '%tempDir%/mails',
        'from' => NULL,
        'returnPath' => NULL,
        'templateFactory' => 'h4kuna\MailManager\Template\TemplateFactory',
        'messageFactory' => 'h4kuna\MailManager\Message\MessageFactory',
        'development' => '%debugMode%',
        'live' => NULL
    );

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $config = $this->getConfig($this->defaults);

        // message factory
        $builder->addDefinition($this->prefix('messageFactory'))
                ->setClass($config['messageFactory'])
                ->setAutowired(FALSE)
                ->addSetup('setFrom', array($config['from']))
                ->addSetup('setReturnPath', array($config['returnPath']));

        // storage factory
        $templateFactory = $builder->addDefinition($this->prefix('templateFactory'));
        $templateFactory->setClass($config['templateFactory'])
                ->setAutowired(FALSE);

        // mailer
        if ($config['development']) {
            $builder->removeDefinition('nette.mailer');
            $mailerBuilder = $builder->addDefinition('nette.mailer')
                    ->setClass('h4kuna\MailManager\Mailer\FileMailer')
                    ->setArguments(array($config['tempDir']));

            if ($config['live'] !== NULL) {
                $mailerBuilder->addSetup('setLive', array($config['live']));
            }
        }

        // MailManager
        $builder->addDefinition($this->prefix('mailManager'))
                ->setClass('h4kuna\MailManager\MailManager')
                ->setArguments(array('@nette.mailer', $this->prefix('@templateFactory'), $this->prefix('@messageFactory')))
                ->addSetup('setImageDir', array($config['imageDir']))
                ->addSetup('setTemplateDir', array($config['templateDir']));

        return $builder;
    }

}
