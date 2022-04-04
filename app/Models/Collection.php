<?php

namespace App\Models;

trait Collection
{

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = is_a($value, self::class) ? $value : self::find($value);
        }
        return $this;
    }

    public function search($column, $value)
    {

        $key = array_search($value,
            array_column($this->content, $column)
        );

        if($key !== false && array_key_exists($key, $this->content)){
            return self::find($this->content[$key]);
        }

        return false;
    }

    public function slice($start, $finish)
    {

        $items = array_slice($this->content, $start, $finish);

        if(count($items) === 1){
            return self::find(array_shift($items));
        }

        return false;
    }

}