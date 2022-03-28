<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

#[AsCommand(
    name: 'app:build-env',
    description: 'Populate the DB with all resources',
    hidden: false,
    aliases: ['app:build-env']
)]

class BuildEnvironment extends Command
{

    protected static $defaultName = 'app:build-env';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        require('config/db.php');

        try {
            $conn = new PDO("mysql:host=$servername", $username, $password);
            $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            echo "<p class='bg-primary rounded'>" . "Database dropped successfully" . "</p>";
        } catch(PDOException $e) {
            echo "<p class='bg-danger rounded'>" . $sql . "<br>" . $e->getMessage() . "</p>";
        }
        $conn = null;

        return Command::SUCCESS;

    }

}

$application = new Application();
$application->add(new BuildEnvironment());
$application->run();