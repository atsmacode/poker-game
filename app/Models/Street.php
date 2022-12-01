<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\PokerGame\Models\Model;

class Street extends Model
{
    use Collection;

    public $table = 'streets';
    public string $name;
    public $id;

    public function __construct($connection, array $data = null)
    {
        parent::__construct($connection, $data);
    }
}