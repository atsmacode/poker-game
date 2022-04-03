<?php

namespace App\Models;

class Stack extends Model
{

    use Collection;

    public $table = 'stacks';
    public string $name;

}