<?php

namespace App\Classes;

use App\Helpers\BetHelper;
use App\Helpers\PotHelper;
use App\Models\Action;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\TableSeat;

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
        $this->handTable = $hand->table();
        $this->street    = null;
        $this->fold      = Action::find(['name' =>'Fold']);
        $this->check     = Action::find(['name' =>'Check']);
        $this->call      = Action::find(['name' =>'Call']);
        $this->bet       = Action::find(['name' =>'Bet']);
        $this->raise     = Action::find(['name' =>'Raise']);
    }

    public function play()
    {

        $this->updateSeatStatusOfLatestAction();
        $this->updateAllOtherSeatsBasedOnLatestAction();

        return $this->nextStep();

    }

    public function showdown()
    {
        // return [
        //     'deck'           => $this->dealer->getDeck(),
        //     'pot'            => $this->hand->pot()->amount,
        //     'communityCards' => $this->getCommunityCards(),
        //     'players'        => $this->getPlayerData(),
        //     'winner'         => true
        // ];
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
            'hand_id' => $this->hand->id
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
        if($this->theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()){
            TableSeat::query()
                ->where(
                    'id',
                    $this->hand->fresh()->playerActions->fresh()->where('active', 1)->where('big_blind', 1)->first()->table_seat_id
                )->update([
                    'can_continue' => 1
                ]);

            return $this->showdown();
        }

        if($this->readyForShowdown() || $this->onePlayerRemainsThatCanContinue()){
            return $this->showdown();
        }

        if($this->allActivePlayersCanContinue()){
            return $this->continue();
        }

        if($this->theLastHandWasCompleted()){
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
        PlayerAction::find(['hand_id' => $this->hand->id])
            ->updateBatch([
                'action_id' => null
            ], 'hand_id = ' . $this->hand->id);
    }

    protected function readyForShowdown()
    {
        return count($this->hand->streets()->content) === count($this->game->streets) &&
            count($this->hand->actions()::find(['active' => 1])->content) ===
            count($this->handTable->seats()::find(['can_continue' => 1])->content);
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return count($this->hand->actions()::find(['active' => 1])->content)
            === count($this->handTable->seats()::find(['can_continue' => 1])->content)
            && count($this->handTable->seats()::find(['can_continue' => 1])->content) === 1;
    }

    protected function allActivePlayersCanContinue()
    {
        return count($this->hand->actions()::find(['active' => 1])->content) ===
            count($this->handTable->seats()::find(['can_continue' => 1])->content);
    }

    protected function theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()
    {
        return count($this->hand->actions()::find(['active' => 1, 'big_blind' => 1])->content) === 1
            && count($this->hand->actions()::find(['active' => 1, 'big_blind' => 0])->content) === 0;
    }

    protected function theLastHandWasCompleted()
    {
        return $this->hand->completed_on;
    }

    protected function allPlayerActionsAreNullSoANewSreetHasBeenSet()
    {
        return !$this->hand->actions()->search('action_id', null);
    }

    protected function getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer)
    {
        $dealer = $this->hand->actions()::find([
            'table_seat_id' => TableSeat::find(['is_dealer' => 1, 'table_id' => $this->handTable->id])->id
        ]);

        $dealerIsActive = $dealer->active ? $dealer : false;

        if($dealerIsActive){

            if($firstActivePlayer->is_dealer){
                $playerAfterDealer = TableSeat::playerAfterDealer(
                    $this->hand->id,
                    $firstActivePlayer->id
                );

                $playerAfterDealer = $playerAfterDealer ?: TableSeat::firstActivePlayer(
                    $this->hand->id,
                    $firstActivePlayer->id
                );
            } else if($firstActivePlayer->id < $dealerIsActive->id){
                $playerAfterDealer = TableSeat::playerAfterDealer(
                    $this->hand->id,
                    $firstActivePlayer->id
                );

                $firstActivePlayer = $playerAfterDealer ?: $firstActivePlayer;
            }
        } else {
            $playerAfterDealer = TableSeat::playerAfterDealer(
                $this->hand->id,
                $firstActivePlayer->id
            );

            $firstActivePlayer = $playerAfterDealer ? $playerAfterDealer->tableSeat : $firstActivePlayer;
        }

        return $firstActivePlayer;
    }

    public function getActionOn()
    {
        $firstActivePlayer = $this->hand->actions()->search('active', 1)->tableSeat();

        if($this->allPlayerActionsAreNullSoANewSreetHasBeenSet()){
            return $this->getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer);
        }

        $lastToAct = $this->hand->actions()->latest();

        $activePlayersAfterLastToAct = array_filter(
            PlayerAction::find(['active' => 1, 'hand_id' => $this->hand->id])->collect()->content,
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

        return $playerAfterLastToAct;

    }

    protected function getPlayerData()
    {
        $playerData = [];

        foreach($this->hand->actions()->collect()->content as $playerAction){
            $actionOnGet = $this->getActionOn();
            $actionOn = false;

            if($actionOnGet && 
            $actionOnGet->player_id 
            === $playerAction->player_id){
                $actionOn = true;
            }

            $actionName = $playerAction->action_id ? $playerAction->action()->name : null;

            $stack = $playerAction->player()->stacks()->search('table_id', $this->handTable->id)
                ? $playerAction->player()->stacks()->search('table_id', $this->handTable->id)->amount
                : null;

            $playerData[] = [
                'stack'            => $stack,
                'name'             => $playerAction->player()->username,
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
            foreach($player->wholeCards()->collect()->searchMultiple('hand_id', $this->hand->id) as $wholeCard){
                $wholeCards[] = [
                    'player_id'        => $wholeCard->player_id,
                    'rank'             => $wholeCard->card()->rank,
                    'suit'             => $wholeCard->card()->suit,
                    'suitAbbreviation' => $wholeCard->card()->suit
                ];
            }

            return $wholeCards;
        }

        foreach(TableSeat::find(['can_continue' => 1]) as $tableSeat){
            foreach($tableSeat->player()->collect()->searchMultiple('hand_id', $this->hand->id) as $wholeCard){
                $wholeCards[] = [
                    'player_id'        => $wholeCard->player_id,
                    'rank'             => $wholeCard->card()->rank,
                    'suit'             => $wholeCard->card()->suit,
                    'suitAbbreviation' => $wholeCard->card()->suit
                ];
            }
        }

        return $wholeCards;
    }

    public function getCommunityCards()
    {
        $cards = [];
        foreach($this->hand->streets()->collect()->content as $street){
            foreach($street->cards()->collect()->content as $streetCard){
                $cards[] = [
                    'rank'             => $streetCard->card()->rank,
                    'suit'             => $streetCard->card()->suit,
                    'suitAbbreviation' => $streetCard->card()->suit
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
        $latestAction = $this->hand->actions()->filter('action_id', $this->fold->id)->latest();

        if($playerAction->active === 1){

            $options = [
                $this->fold
            ];

            switch(PlayerAction::find(['table_seat_id' => $latestAction])->action_id){
                case $this->call->id:
                    /*
                     * BB can only check if there were no raises before the latest call action.
                     */
                    if(
                        $playerAction->big_blind === 1 &&
                        !$this->hand->actions()->search('action_id', $this->raise->id)
                    ){
                        array_push($options, $this->check, $this->raise);
                    } else {
                        array_push($options, $this->call, $this->raise);
                    }
                    break;
                case $this->bet->id:
                case $this->raise->id:
                    array_push($options, $this->call, $this->raise);
                    break;
                case $this->check->id:
                default:
                    array_push($options, $this->check, $this->bet);
                    break;
            }

        }

        return $options;
    }

    public function updateAllOtherSeatsBasedOnLatestAction()
    {
        $latestAction = PlayerAction::find(['table_seat_id' => $this->hand->actions()->latest()]);

        // Update the other table seat statuses accordingly
        switch($latestAction->action_id){
            case $this->bet->id:
            case $this->raise->id:
                $canContinue = 0;
                break;
            default:
                break;
        }

        if(isset($canContinue)){
            $tableSeats = TableSeat::find(['table_id' => $this->handTable->id]);
            $tableSeats->updateBatch(['can_continue' => $canContinue], 'table_seat_id != ' . $latestAction->table_seat_id);
        }
    }

    public function updateSeatStatusOfLatestAction()
    {
        $latestAction = PlayerAction::find(['table_seat_id' => $this->hand->actions()->latest()]);

        // Update the table seat status of the latest action accordingly
        switch($latestAction->action_id){
            case $this->check->id:
            case $this->call->id:
            case $this->bet->id:
            case $this->raise->id:
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
            'hand_id' => $this->hand->id
        ]);

        foreach($this->handTable->seats()->collect()->content as $seat){
            $seat->player()->actions()::create([
                'player_id' => $seat->player_id,
                'hand_street_id' => $this->street->id,
                'table_seat_id' => $seat->id,
                'hand_id' => $this->hand->id,
                'active' => 1
            ]);

            PlayerAction::find([
                'hand_street_id' => $this->street->id,
                'table_seat_id' => $seat->id,
                'hand_id' => $this->hand->id,
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
        return $this->handTable->seats()->search('id', $currentDealer->id + 2) &&
            $this->handTable->seats()->search('id', $currentDealer->id + 3);
    }

    protected function thereIsOneSeatAfterTheDealer($currentDealer)
    {
        return $this->handTable->seats()->search('id', $currentDealer->id + 1) &&
            $this->handTable->seats()->search('id', $currentDealer->id + 2);
    }

    protected function identifyTheNextDealerAndBlindSeats($currentDealer)
    {
        if($currentDealer){
            $currentDealer = $this->handTable->seats()->search('id', $currentDealer);
        } else {
            $currentDealer = $this->handTable->seats()->search('is_dealer', 1);
        }

        if($this->noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)){

            $dealer = $this->handTable->seats()->slice(0, 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 2);

        } else if($this->thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 2);

        } else if($this->thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->search('id', $dealer->id + 1);
            $bigBlindSeat = $this->handTable->seats()->slice(0, 1);

        } else {

            $dealer = $this->handTable->seats()->search('id', $currentDealer->id + 1);
            $smallBlindSeat = $this->handTable->seats()->slice(0, 1);
            $bigBlindSeat = $this->handTable->seats()->slice(1, 1);

        }

        return [
            'currentDealer' => $currentDealer,
            'dealer' => $dealer,
            'smallBlindSeat' => $smallBlindSeat,
            'bigBlindSeat' => $bigBlindSeat
        ];
    }

    public function setDealerAndBlindSeats($currentDealer = null)
    {
        if(count($this->hand->streets()->content) === 1){
            $bigBlind = PlayerAction::find([
                'hand_id' => $this->hand->id,
                'big_blind' => 1
            ]);

            if($bigBlind->isNotEmpty()){
                $bigBlind->update([
                    'big_blind' => 0
                ]);
            }
        }

        [
            'currentDealer' => $currentDealer,
            'dealer' => $dealer,
            'smallBlindSeat' => $smallBlindSeat,
            'bigBlindSeat' => $bigBlindSeat
        ] = $this->identifyTheNextDealerAndBlindSeats($currentDealer);

        if($currentDealer){
            $tableSeat = TableSeat::find([
                'id' =>  $currentDealer->id,
                'table_id' => $this->handTable->id
            ]);

            $tableSeat->update([
                'is_dealer' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('- 20 seconds'))
            ]);
        }

        $tableSeat = TableSeat::find([
            'id' =>  $dealer->id,
            'table_id' => $this->handTable->id
        ]);

        $tableSeat->update([
            'is_dealer' => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 18 seconds'))
        ]);

        $smallBlind = PlayerAction::find([
            'player_id' =>  $smallBlindSeat->player()->id,
            'table_seat_id' =>  $smallBlindSeat->id,
            'hand_street_id' => HandStreet::find([
                'street_id' => Street::find(['name' => $this->game->streets[0]['name']])->id,
                'hand_id' => $this->hand->id
            ])->id
        ]);

        $bigBlind = PlayerAction::find([
            'player_id' =>  $bigBlindSeat->player()->id,
            'table_seat_id' =>  $bigBlindSeat->id,
            'hand_street_id' => HandStreet::find([
                'street_id' => Street::find(['name' => $this->game->streets[0]['name']])->id,
                'hand_id' => $this->hand->id
            ])->id
        ]);

        BetHelper::postBlinds($this->hand, $smallBlind, $bigBlind);
    }
}
