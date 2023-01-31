<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\Framework\Database\Database;
use Doctrine\DBAL\Schema\Schema;

class CreateActions extends Database
{
    public static array $methods = [
        'createActionsTable',
    ];

    public function createActionsTable()
    {
        try {
            $schema  = new Schema();
            $myTable = $schema->createTable('actions');

            $myTable->addColumn('id', 'integer', ['unsigned' => true])
                ->setAutoincrement(true);

            $myTable->addColumn('name', 'string', ['length' => 32])
                ->setNotnull(true);

            $myTable->setPrimaryKey(['id']);

            $dbPlatform = $this->connection->getDatabasePlatform();
            $sql        = $schema->toSql($dbPlatform);

            $this->connection->exec(array_shift($sql));
        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
