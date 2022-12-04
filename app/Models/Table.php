<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Table extends Model
{
    use Collection, CanBeModelled;

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