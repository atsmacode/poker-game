<?php

namespace App\Models;

class Action extends Model
{
    use Collection;

    public $table = 'actions';
    public string $name;
    public int $id;
}
