<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreatePots
{

    public static array $methods = [
        'createPotsTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createPotsTable()
    {

        $sql = "CREATE TABLE pots (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount INT(12) UNSIGNED NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (hand_id) REFERENCES hands(id)
        )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Pots table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }


}