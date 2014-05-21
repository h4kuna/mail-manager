<?php

namespace h4kuna\MailManager\Template;

use Nette\DI\Container;

/**
 *
 * @author Milan Matějček
 */
class TemplateFactory implements ITemplateFactory {

    /** @var Container */
    private $container;

    function __construct(Container $container) {
        $this->container = $container;
    }

    public function create() {
        return $this->container->createService('nette.template');
    }

}
