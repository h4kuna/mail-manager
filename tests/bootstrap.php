<?php

use Nette\Utils\FileSystem;

include __DIR__ . "/../vendor/autoload.php";

function dd($var /* ... */)
{
    Tracy\Debugger::enable(FALSE);
    foreach (func_get_args() as $arg) {
        \Tracy\Debugger::dump($arg);
    }
    exit;
}

Tester\Environment::setup();

// 2# Create Nette Configurator
$configurator = new Nette\Configurator;

$tmp = __DIR__ . '/temp/' . php_sapi_name();
FileSystem::createDir($tmp, 0755);
FileSystem::createDir($tmp . '/cache/latte', 0755);
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);
$configurator->setDebugMode(FALSE);
$configurator->addConfig(__DIR__ . '/test.neon');
$container = $configurator->createContainer();

return $container;



