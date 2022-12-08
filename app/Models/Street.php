<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Street extends Model
{
    use Collection;

    public        $table = 'streets';
    public string $name;
    public        $id;
}
