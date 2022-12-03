<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Dbal\Model;
use Atsmacode\PokerGame\PokerGameConfigProvider;

class PokerGameModel extends Model
{
    public function __construct(array $data = null)
    {
        parent::__construct(new PokerGameConfigProvider());
        
        $this->data = $data;
    }
}
