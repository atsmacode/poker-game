<?php

namespace App\Models;

class Table extends Model
{

    protected $table = 'tables';
    public string $name;
    public $content;
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
            $this->content[$key] = new self($value);
        }
        return $this;
    }

    public function seats($stop = false)
    {
        return new TableSeat(['table_id' => $this->id], $stop);
    }

}