<?php

namespace Atsmacode\PokerGame\Controllers\Dev\PotLimitHoldEm;

use Atsmacode\PokerGame\Controllers\Dev\HandController as BaseHandController;
use Atsmacode\PokerGame\Game\PotLimitHoldEm;

class HandController extends BaseHandController
{
    protected string $game = PotLimitHoldEm::class;
}
