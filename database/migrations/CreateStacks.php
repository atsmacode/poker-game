<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateStacks extends Database
{

    public static array $methods = [
        'createStacksTable',
    ];

    public function createStacksTable($output, $showMessages = true)
    {

        /**
         * TODO amount is not unsigned to allow negative
         * values until 'player loses/zero-chips feature
         * is added.
         */
        $sql = "CREATE TABLE stacks (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount BIGINT(12) NULL,
            player_id INT(6) UNSIGNED NOT NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (table_id) REFERENCES tables(id)
        )";

        try {
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Stacks table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }


}
