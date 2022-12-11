<?php

namespace Atsmacode\PokerGame\Controllers\PotLimitOmaha;

use Atsmacode\PokerGame\Controllers\HandController as BaseHandController;
use Atsmacode\PokerGame\Game\PotLimitOmaha;

class HandController extends BaseHandController
{
    protected string $game = PotLimitOmaha::class;
}
