<?php

use Nette\Utils;

include __DIR__ . "/../vendor/autoload.php";

Tester\Environment::setup();

// 2# Create Nette Configurator
$configurator = new Nette\Configurator;

Salamium\Testinium\File::setRoot(__DIR__ . '/data');

$tmp = __DIR__ . '/temp';
$logDir = $tmp . '/log';
Utils\FileSystem::createDir($logDir);
Utils\FileSystem::createDir($tmp . '/cache/latte');
$configurator->enableDebugger($logDir);
$configurator->setTempDirectory($tmp);
$configurator->setDebugMode(TRUE);
$configurator->addConfig(__DIR__ . '/test.neon');
$container = $configurator->createContainer();

Tracy\Debugger::enable(FALSE);

return $container;



