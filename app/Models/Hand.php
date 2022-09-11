<?php

namespace App\Models;

use PDOException;

class Hand extends Model
{
    use Collection;

    protected $table = 'hands';
    public $id;

    public function streets()
    {
        return HandStreet::find(['hand_id' => $this->id]);
    }

    public function table()
    {
        return Table::find(['id' => $this->table_id]);
    }

    public function actions()
    {
        return PlayerAction::find(['hand_id' => $this->id]);
    }

    public function pot()
    {
        return Pot::find(['hand_id' => $this->id]);
    }

    public function complete()
    {
        $query = sprintf("
            UPDATE
                hands
            SET
                completed_on = NOW()
            WHERE
                id = {$this->id}
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}