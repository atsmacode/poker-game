<?php

namespace App\Models;

class HandType extends Model
{
    use Collection;

    protected $table = 'hand_types';
    public string $name;
    public $ranking;
}