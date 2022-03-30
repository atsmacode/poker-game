<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use PDO;
use PDOException;

class CreatePlayerActions
{

    use Connect;

    public static array $methods = [
        'createPlayerActionsTable',
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function createPlayerActionsTable($output)
    {

        $sql = "CREATE TABLE player_actions (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            bet_amount INT(6) UNSIGNED NULL,
            active BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NOT NULL,
            action_id INT(6) UNSIGNED NOT NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            hand_street_id INT(6) UNSIGNED NOT NULL,
            table_seat_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (action_id) REFERENCES actions(id),
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            FOREIGN KEY (hand_street_id) REFERENCES hand_streets(id),
            FOREIGN KEY (table_seat_id) REFERENCES table_seats(id)
        )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Player actions table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }


}