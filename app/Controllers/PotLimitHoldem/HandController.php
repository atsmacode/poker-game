<?php

namespace Atsmacode\PokerGame\Controllers\PotLimitHoldEm;

use Atsmacode\PokerGame\Controllers\HandController as BaseHandController;
use Atsmacode\PokerGame\Game\PotLimitHoldEm;

class HandController extends BaseHandController
{
    protected string $game = PotLimitHoldEm::class;
}
