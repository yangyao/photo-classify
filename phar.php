<?php


require __DIR__.'/vendor/autoload.php';

$command = 'php index.php app:run '.$argv[1].' '.$argv[2];

$process = new \Symfony\Component\Process\Process($command);
$process->run();