<?php

include __DIR__ . "/../vendor/autoload.php";

if (!class_exists('\Tester\Environment')) {
    echo 'Install Nette Tester using `composer update --dev`';
    exit(1);
}

$configurator = new Nette\Configurator;
$tmp = __DIR__ . '/temp/' . php_sapi_name();
@mkdir($tmp, 0777, TRUE);
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);
$configurator->setDebugMode();

$configurator->addConfig(__DIR__ . '/config.neon');

$container = $configurator->createContainer();

Tester\Environment::setup();

return $container;



