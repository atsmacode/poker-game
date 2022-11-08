<?php

namespace App\Classes;

use App\Helpers\BetHelper;
use App\Helpers\PotHelper;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\TableSeat;
use App\Constants\Action;

class GamePlay
{

    public $game;
    public $dealer;
    protected $actionOn;
    public $fold;
    public $check;
    public $call;
    public $bet;
    public $raise;

    public function __construct($hand, $deck = null)
    {
        $this->game      = new PotLimitHoldEm();
        $this->dealer    = (new Dealer())->setDeck($deck);
        $this->hand      = $hand;
        $this->handId    = $hand->id;
        $this->handTable = $hand->table();
        $this->street    = null;
    }

    public function play()
    {
        $this->updateSeatStatusOfLatestAction();
        $this->updateAllOtherSeatsBasedOnLatestAction();

        return $this->nextStep();

    }

    public function showdown()
    {
        $winner = (new Showdown($this->hand))->compileHands()->decideWinner();

        PotHelper::awardPot($this->hand->pot(), $winner['player']);

        $this->hand->complete();

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->hand->pot()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => $winner
        ];
    }

    public function continue()
    {
        $this->updatePlayerStatusesOnNewStreet();

        // Not keen on the way I'm adding/subtracting from the handStreets->count() to match array starting with 0
        $this->street = HandStreet::create([
            'street_id' => Street::find(['name' => $this->game->streets[count($this->hand->streets()->content)]['name']])->id,
            'hand_id' => $this->handId
        ]);

        $this->dealer->dealStreetCards(
            $this->street,
            $this->game->streets[count($this->hand->streets()->content) - 1]['community_cards']
        );

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->hand->pot()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => null
        ];
    }

    public function start($currentDealer = null)
    {
        $this->initiateStreetActions();
        $this->initiatePlayerStacks();
        $this->setDealerAndBlindSeats($currentDealer);

        $this->dealer->setDeck()->shuffle();

        if($this->game->streets[0]['whole_cards']){
            $this->dealer->dealTo(
                $this->handTable->players(),
                $this->game->streets[0]['whole_cards'],
                $this->hand,
            );
        }

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->hand->pot()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => null
        ];
    }

    public function nextStep()
    {
        if ($this->theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()) {
            TableSeat::bigBlindWins($this->handId);

            return $this->showdown();
        }

        if ($this->readyForShowdown() || $this->onePlayerRemainsThatCanContinue()) {
            return $this->showdown();
        }

        if ($this->allActivePlayersCanContinue()) {
            return $this->continue();
        }

        if ($this->theLastHandWasCompleted()) {
            return $this->start();
        }

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->hand->pot()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => null
        ];
    }

    protected function updatePlayerStatusesOnNewStreet()
    {
        /*
         * Reset can_continue & BB status once pre-flop action and/or previous street is finished.
         */
        TableSeat::find(['table_id' => $this->handTable->id])
            ->updateBatch([
                'can_continue' => 0
            ], 'table_id = ' . $this->handTable->id);

        /*
         * Always reset action_id.
         */
        PlayerAction::find(['hand_id' => $this->handId])
            ->updateBatch([
                'action_id' => null
            ], 'hand_id = ' . $this->handId);
    }

    protected function readyForShowdown()
    {
        return count($this->hand->streets()->content) === count($this->game->streets) &&
            count($this->hand->getActivePlayers()) ===
            count($this->hand->getContinuingPlayers());
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return count($this->hand->getActivePlayers())
            === count($this->hand->getContinuingPlayers())
            && count($this->hand->getContinuingPlayers()) === 1;
    }

    protected function allActivePlayersCanContinue()
    {
        return count($this->hand->getActivePlayers()) ===
            count($this->hand->getContinuingPlayers());
    }

    protected function theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()
    {
        $bigBlindActive = PlayerAction::find([
            'active'    => 1,
            'big_blind' => 1,
            'hand_id'   => $this->handId
        ])->content;

        $nonBigBlindActive = PlayerAction::find([
            'active'    => 1,
            'big_blind' => 0,
            'hand_id'   => $this->handId
        ])->content;

        return count($bigBlindActive) === 1 && count($nonBigBlindActive) === 0;
    }

    protected function theLastHandWasCompleted()
    {
        return $this->hand->completed_on;
    }

    protected function allPlayerActionsAreNullSoANewSreetHasBeenSet()
    {
        return count($this->hand->actions()->content)
            === count($this->hand->getNullActions());
    }

    protected function getThePlayerActionShouldBeOnForANewStreet(TableSeat $firstActivePlayer)
    {
        $dealer = $this->hand->getDealer();

        $playerAfterDealer = TableSeat::playerAfterDealer(
            $this->handId,
            $dealer->table_seat_id
        );

        if (!isset($playerAfterDealer->player_id)) {
            $playerAfterDealer = null;
        }

        return $playerAfterDealer ?: $firstActivePlayer;
    }

    public function getActionOn(): TableSeat
    {
        $firstActivePlayer = TableSeat::firstActivePlayer($this->handId);

        if($this->allPlayerActionsAreNullSoANewSreetHasBeenSet()){
            return $this->getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer);
        }

        $lastToAct = $this->hand->actions()->latest();

        $activePlayersAfterLastToAct = array_filter(
            PlayerAction::find(['active' => 1, 'hand_id' => $this->handId])->collect()->content,
            function($value) use($lastToAct){
                return $value->table_seat_id > $lastToAct;
            }
        );

        $playerAfterLastToAct = count($activePlayersAfterLastToAct)
            ? array_shift($activePlayersAfterLastToAct)
            : null;

        if(!$playerAfterLastToAct){
            return $firstActivePlayer;
        }

        return $playerAfterLastToAct->tableSeat();
    }

    protected function getPlayerData()
    {
        $playerData  = [];
        $actionOnGet = $this->getActionOn();

        foreach(PlayerAction::find(['hand_id' => $this->handId])->collect()->content as $playerAction){
            $actionOn = false;

            if($actionOnGet && $actionOnGet->player_id === $playerAction->player_id){
                $actionOn = true;
            }

            $actionName = $playerAction->action_id ? $playerAction->action()->name : null;

            $stack = $playerAction->player()->stacks()->search('table_id', $this->handTable->id)
                ? $playerAction->player()->stacks()->search('table_id', $this->handTable->id)->amount
                : null;

            $playerData[] = [
                'stack'            => $stack,
                'name'             => $playerAction->player()->name,
                'action_id'        => $playerAction->action_id,
                'action_name'      => $actionName ,
                'player_id'        => $playerAction->player_id,
                'table_seat_id'    => $playerAction->table_seat_id,
                'hand_street_id'   => $playerAction->hand_street_id,
                'bet_amount'       => $playerAction->bet_amount,
                'active'           => $playerAction->active,
                'can_continue'     => $playerAction->tableSeat()->can_continue,
                'is_dealer'        => $playerAction->tableSeat()->is_dealer,
                'big_blind'        => $playerAction->big_blind,
                'small_blind'      => $playerAction->small_blind,
                'whole_cards'      => $this->getWholeCards($playerAction->player()),
                'action_on'        => $actionOn,
                'availableOptions' => $this->getAvailableOptionsBasedOnLatestAction($playerAction)
            ];
        }

        return $playerData;
    }

    public function getWholeCards($player = null)
    {
        $wholeCards = [];

        if(isset($player)){
            foreach($player->getWholeCards($this->handId) as $wholeCard){
                $wholeCards[] = [
                    'player_id'        => $wholeCard['player_id'],
                    'rank'             => $wholeCard['rank'],
                    'rankAbbreviation' => $wholeCard['rankAbbreviation'],
                    'suit'             => $wholeCard['suit'],
                    'suitAbbreviation' => $wholeCard['suitAbbreviation']
                ];
            }

            return $wholeCards;
        }

        return $wholeCards;
    }

    public function getCommunityCards()
    {
        $cards = [];
        foreach($this->hand->streets()->collect()->content as $street){
            foreach($street->cards()->collect()->content as $streetCard){
                $cards[] = [
                    'rankAbbreviation' => $streetCard->getCard()['rankAbbreviation'],
                    'suit'             => $streetCard->getCard()['suit'],
                    'suitAbbreviation' => $streetCard->getCard()['suitAbbreviation']
                ];
            }
        }

        return $cards;
    }

    public function getAvailableOptionsBasedOnLatestAction($playerAction)
    {
        $options = [];

        /*
         * We only need to update the available actions if a player did something other than fold.
         */
        $latestAction = $this->hand->getLatestAction();

        if (!$latestAction) {
            array_push($options, Action::FOLD, Action::CHECK, Action::BET);
            return $options;
        }

        if ($playerAction->active === 1) {

            $options = [
                Action::FOLD
            ];

            switch(PlayerAction::find([
                'table_seat_id' => $latestAction['id'],
                'hand_id' => $this->handId
            ])->action_id){
                case Action::CALL['id']:
                    /*
                     * BB can only check if there were no raises before the latest call action.
                     */
                    if(
                        $playerAction->big_blind === 1 &&
                        !$this->hand->actions()->search('action_id', Action::RAISE['id'])
                    ){
                        array_push($options, Action::CHECK, Action::RAISE);
                    } else {
                        array_push($options, Action::CALL, Action::RAISE);
                    }
                    break;
                case Action::BET['id']:
                case Action::RAISE['id']:
                    array_push($options, Action::CALL, Action::RAISE);
                    break;
                case Action::CHECK['id']:
                default:
                    array_push($options, Action::CHECK, Action::BET);
                    break;
            }

        }

        return $options;
    }

    public function updateAllOtherSeatsBasedOnLatestAction()
    {
        $latestAction = PlayerAction::find([
            'hand_id'       => $this->handId,
            'table_seat_id' => $this->hand->actions()->latest()
        ]);

        // Update the other table seat statuses accordingly
        switch($latestAction->action_id){
            case Action::BET['id']:
            case Action::RAISE['id']:
                $canContinue = 0;
                break;
            default:
                break;
        }

        if(isset($canContinue)){
            $tableSeats = TableSeat::find(['table_id' => $this->handTable->id]);
            $tableSeats->updateBatch(['can_continue' => $canContinue], 'id != ' . $latestAction->table_seat_id);
        }
    }

    public function updateSeatStatusOfLatestAction()
    {
        $latestAction = PlayerAction::find([
            'hand_id'       => $this->handId,
            'table_seat_id' => $this->hand->actions()->latest()
        ]);

        // Update the table seat status of the latest action accordingly
        switch($latestAction->action_id){
            case Action::CHECK['id']:
            case Action::CALL['id']:
            case Action::BET['id']:
            case Action::RAISE['id']:
                $canContinue = 1;
                break;
            default:
                $canContinue = 0;
                break;
        }

        TableSeat::find(['id' => $latestAction->table_seat_id])
            ->update([
                'can_continue' => $canContinue
            ]);
    }

    public function initiateStreetActions()
    {
        $this->street = HandStreet::create([
            'street_id' => Street::find(['name' => 'Pre-flop'])->id,
            'hand_id'   => $this->handId
        ]);

        foreach($this->handTable->seats()->collect()->content as $seat){
            $seat->player()->actions()::create([
                'player_id'      => $seat->player_id,
                'hand_street_id' => $this->street->id,
                'table_seat_id'  => $seat->id,
                'hand_id'        => $this->handId,
                'active'         => 1
            ]);

            PlayerAction::find([
                'hand_street_id' => $this->street->id,
                'table_seat_id'  => $seat->id,
                'hand_id'        => $this->handId,
            ])->update([
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 seconds')) // For testing so I can get the latest action, otherwise they are all the same
            ]);

        }

        return $this;
    }

    public function initiatePlayerStacks()
    {
        foreach($this->handTable->seats()->collect()->content as $seat){
            if(count($seat->player()->stacks()->content) === 0){
                $seat->player()->stacks()::create([
                    'amount' => 1000,
                    'player_id' => $seat->player_id,
                    'table_id' => $this->handTable->id
                ]);
            }
        }

        return $this;
    }

    protected function noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)
    {
        return !$currentDealer || !$this->handTable->seats()->search('id', $currentDealer->id + 1);
    }

    protected function thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->handTable->seats()->search('id', $currentDealer->id + 3);
    }

    protected function thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->handTable->seats()->search('id', $currentDealer->id + 2);
    }

    protected function thereIsOneSeatAfterTheDealer($currentDealer)
    {
        return $this->handTable->seats()->search('id', $currentDealer->id + 1);
    }

    protected function identifyTheNextDealerAndBlindSeats($currentDealer)
    {
        if($currentDealer){
            $currentDealer = $this->handTable->seats()->search('id', $currentDealer->id);
        } else {
            $currentDealer = $this->handTable->seats()->search('is_dealer', 1);
        }

        /**
         * TODO: these methods must currently be called
         * in order. Consider changing. Also will only
         * work if all seats have a stack/player.
         */
        if($this->noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)){
            
            $dealer         = $this->handTable->seats()->slice(0, 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat   = $this->handTable->seats()->search('id', $dealer->id + 2);

        } else if($this->thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat   = $this->handTable->seats()->search('id', $dealer->id + 2);

        } else if($this->thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat   = $this->handTable->seats()->slice(0, 1);

        } else {

            $dealer         = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->slice(0, 1);
            $bigBlindSeat   = $this->handTable->seats()->slice(1, 1);

        }

        return [
            'currentDealer'  => $currentDealer,
            'dealer'         => $dealer,
            'smallBlindSeat' => $smallBlindSeat,
            'bigBlindSeat'   => $bigBlindSeat
        ];
    }

    public function setDealerAndBlindSeats($currentDealer = null)
    {
        if(count($this->hand->streets()->content) === 1){
            $bigBlind = PlayerAction::find([
                'hand_id' => $this->handId,
                'big_blind' => 1
            ]);

            if($bigBlind->isNotEmpty()){
                $bigBlind->update([
                    'big_blind' => 0
                ]);
            }
        }

        [
            'currentDealer'      => $currentDealer,
            'dealer'             => $dealer,
            'smallBlindSeat'     => $smallBlindSeat,
            'bigBlindSeat'       => $bigBlindSeat
        ] = $this->identifyTheNextDealerAndBlindSeats($currentDealer);

        if($currentDealer){
            $tableSeat     = TableSeat::find([
                'id'       =>  $currentDealer->id,
                'table_id' => $this->handTable->id
            ]);

            $tableSeat->update([
                'is_dealer'  => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('- 20 seconds'))
            ]);
        }
        
        $tableSeat = TableSeat::find([
            'id'       =>  $dealer->id,
            'table_id' => $this->handTable->id
        ]);

        $tableSeat->update([
            'is_dealer'  => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 18 seconds'))
        ]);

        $smallBlind = PlayerAction::find([
            'player_id'      =>  $smallBlindSeat->player()->id,
            'table_seat_id'  =>  $smallBlindSeat->id,
            'hand_street_id' => HandStreet::find([
                'street_id'  => Street::find(['name' => $this->game->streets[0]['name']])->id,
                'hand_id' => $this->handId
            ])->id
        ]);

        $bigBlind = PlayerAction::find([
            'player_id'      =>  $bigBlindSeat->player()->id,
            'table_seat_id'  =>  $bigBlindSeat->id,
            'hand_street_id' => HandStreet::find([
                'street_id'  => Street::find(['name' => $this->game->streets[0]['name']])->id,
                'hand_id' => $this->handId
            ])->id
        ]);

        BetHelper::postBlinds($this->hand, $smallBlind, $bigBlind);
    }
}
