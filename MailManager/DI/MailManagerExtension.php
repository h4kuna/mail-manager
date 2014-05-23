<?php

namespace h4kuna\MailManager\DI;

use Nette\DI\CompilerExtension;

class MailManagerExtension extends CompilerExtension {

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

    public function loadConfiguration() {
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
            $mailerBuilder = $builder->addDefinition($this->prefix('developmentMailer'))
                            ->setClass('h4kuna\MailManager\Mailer\FileMailer')
                            ->setArguments(array($config['tempDir']))
                            ->setAutowired(FALSE);

            if ($config['live'] !== NULL) {
                $mailerBuilder->addSetup('setLive', array($config['live']));
            }

            $mailer = $this->prefix('@developmentMailer');
        } else {
            $mailer = '@nette.mailer';
        }

        // MailManager
        $builder->addDefinition($this->prefix('mailManager'))
                ->setClass('h4kuna\MailManager')
                ->setArguments(array($mailer, $this->prefix('@templateFactory'), $this->prefix('@messageFactory')))
                ->addSetup('setImageDir', array($config['imageDir']))
                ->addSetup('setTemplateDir', array($config['templateDir']));

        return $builder;
    }

}
