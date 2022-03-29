<?php

require __DIR__.'/vendor/autoload.php';

use App\Console\Commands\BuildEnvironment;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new BuildEnvironment());
$application->run();