<?php

namespace App\Models;

class Street extends Model
{
    use Collection;

    public $table = 'streets';
    public string $name;
    public $id;

}