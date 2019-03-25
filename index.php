<?php


require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Yangyao\PhotoClassify\Command\ClassifyCommand;

$application = new Application();

$application->add(new ClassifyCommand());

$application->run();