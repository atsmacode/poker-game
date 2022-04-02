<?php

namespace App\Models;

class HandStreet extends Model
{

    protected $table = 'hand_streets';
    public $id;

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

    public function cards($stop = false)
    {
        self::__construct($this->data, $stop);

        return new HandStreetCard(['hand_street_id' => $this->id], $stop);
    }

}