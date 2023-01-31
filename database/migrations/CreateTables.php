<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\Framework\Database\Database;
use Doctrine\DBAL\Schema\Schema;

class CreateTables extends Database
{
    public static array $methods = [
        'createTablesTable',
        'createTableSeatsTable'
    ];

    public function createTablesTable(): void
    {
        try {
            $schema = new Schema();
            $table  = $schema->createTable('tables');

            $table->addColumn('id', 'integer', ['unsigned' => true])->setAutoincrement(true);
            $table->addColumn('name', 'string')->setNotnull(true);
            $table->addColumn('seats', 'integer')->setNotnull(true);
            $table->setPrimaryKey(['id']);

            $dbPlatform = $this->connection->getDatabasePlatform();
            $sql        = $schema->toSql($dbPlatform);

            $this->connection->exec(array_shift($sql));
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function createTableSeatsTable(): void
    {
        $sql = "CREATE TABLE table_seats (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            number INT(6) UNSIGNED NULL,
            can_continue BOOLEAN DEFAULT 0,
            is_dealer BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            updated_at DATETIME NULL,
            FOREIGN KEY (table_id) REFERENCES tables(id),
            FOREIGN KEY (player_id) REFERENCES players(id)
        )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
