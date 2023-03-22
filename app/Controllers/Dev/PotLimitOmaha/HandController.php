<?php

namespace Atsmacode\PokerGame\Controllers\Dev\PotLimitOmaha;

use Atsmacode\PokerGame\Controllers\Dev\HandController as BaseHandController;
use Atsmacode\PokerGame\Game\PotLimitOmaha;

class HandController extends BaseHandController
{
    protected string $game = PotLimitOmaha::class;
}
