<?php declare(strict_types=1);

use Nette\Utils;

include __DIR__ . "/../vendor/autoload.php";

Tester\Environment::setup();

$configurator = new Nette\Configurator;

Salamium\Testinium\File::setRoot(__DIR__ . '/data');

define('TEMP_DIR', __DIR__ . '/temp');

Utils\FileSystem::createDir(TEMP_DIR . '/cache/latte');
$configurator->enableDebugger(TEMP_DIR . '/..');
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(true);
$configurator->addConfig(__DIR__ . '/config/test.neon');
$container = $configurator->createContainer();

Tracy\Debugger::enable(false);

return $container;



