<?php

namespace App\Models;

use PDO;
use PDOException;

class PlayerActionLog extends Model
{

    use Collection;

    protected $table = 'player_action_logs';
    public $id;
}