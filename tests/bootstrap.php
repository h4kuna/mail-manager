<?php

use Nette\Utils;

include __DIR__ . "/../vendor/autoload.php";

Tester\Environment::setup();

// 2# Create Nette Configurator
$configurator = new Nette\Configurator;

Salamium\Testinium\File::setRoot(__DIR__ . '/data');

define('TEMP_DIR', __DIR__ . '/temp/' . getmypid());

Utils\FileSystem::createDir(TEMP_DIR . '/cache/latte');
$configurator->enableDebugger(TEMP_DIR . '/..');
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(true);
$configurator->addConfig(__DIR__ . '/config/test.neon');
$container = $configurator->createContainer();

Tracy\Debugger::enable(false);

return $container;



