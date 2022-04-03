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

}