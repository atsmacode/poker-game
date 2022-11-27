<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class Table extends Model
{
    use Collection;

    protected $table = 'tables';
    public string $name;
    public $content;
    public $id;

    public function seats()
    {
        return TableSeat::find(['table_id' => $this->id]);
    }

    public function players()
    {
        $players = new Player([]);

        foreach($this->seats()->collect()->content as $seat){
            $players->content[] = $seat->player();
        }

        return $players;
    }

}