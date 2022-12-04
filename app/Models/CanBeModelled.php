<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\PokerGameConfigProvider;

trait CanBeModelled
{
    public function __construct(array $data = null)
    {
        parent::__construct(new PokerGameConfigProvider());
        
        $this->data = $data;
    }
}
