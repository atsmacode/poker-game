<?php

namespace App\Models;

class Hand extends Model
{

    protected $table = 'hands';

    public function __construct(array $data = null)
    {
        $this->data = $data;
        $this->initiate();
    }

    public function initiate()
    {
        $this->findOrCreate($this->data);
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = is_a($value, self::class) ? $value : new self($value);
        }
        return $this;
    }

}