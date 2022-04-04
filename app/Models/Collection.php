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

    public function searchMultiple($column, $value)
    {

        $keys = array_keys(
            array_column($this->content, $column),
            $value
        );

        if($keys > 0){
            array_filter($this->content, function($key) use($keys){
                return in_array($key, $keys);
            }, ARRAY_FILTER_USE_KEY);

            return $this->content;
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

    public function latest()
    {
        $dates = array_column($this->content, 'updated_at');

        uasort($dates, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        });

        return array_shift($dates);
    }

}