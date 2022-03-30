<?php

namespace App\Classes;

class GamePlay
{
    public $game;
    public $dealer;
    protected $actionOn;
    protected $fold;
    protected $check;
    protected $call;
    protected $bet;
    protected $raise;

    public function __construct($hand, $deck = null)
    {
        $this->game = new PotLimitHoldEm();
        $this->dealer = (new Dealer())->setDeck($deck);
        $this->hand = $hand;
        $this->handTable = new Table();
        $this->street = null;
        $this->fold = new Action('Fold');
        $this->check = new Action('Check');
        $this->call = new Action('Call');
        $this->bet = new Action('Bet');
        $this->raise = new Action('Raise');

        //$this->handTable->hands()->save($this->hand);
    }

    public function play()
    {

        $this->updateSeatStatusOfLatestAction();
        $this->updateAllOtherSeatsBasedOnLatestAction();

        return $this->nextStep();

    }

    public function showdown()
    {

        $winner = (new Showdown($this->hand->fresh()))->compileHands()->decideWinner();

        PotHelper::awardPot($this->hand->fresh()->pot, $winner['player']);

        $this->hand->complete();

        return [
            'deck' => $this->dealer->getDeck(),
            'pot' => $this->hand->fresh()->pot->amount,
            'communityCards' => $this->getCommunityCards(),
            'players' => $this->getPlayerData(),
            'winner' => $winner
        ];
    }

    public function continue()
    {

        $this->updatePlayerStatusesOnNewStreet();

        // Not keen on the way I'm adding/subtracting from the handStreets->count() to match array starting with 0
        $this->street = HandStreet::create([
            'street_id' => Street::where('name', $this->game->streets[$this->hand->fresh()->streets->count()]['name'])->first()->id,
            'hand_id' => $this->hand->id
        ]);

        $this->dealer->dealStreetCards(
            $this->street,
            $this->game->streets[$this->hand->fresh()->streets->count() - 1]['community_cards']
        );


        return [
            'deck' => $this->dealer->getDeck(),
            'pot' => $this->hand->fresh()->pot->amount,
            'communityCards' => $this->getCommunityCards(),
            'players' => $this->getPlayerData(),
            'winner' => null
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
                $this->handTable->fresh()->players,
                $this->game->streets[0]['whole_cards'],
                $this->hand,
            );
        }

        return [
            'deck' => $this->dealer->getDeck(),
            'pot' => $this->hand->fresh()->pot->amount,
            'communityCards' => $this->getCommunityCards(),
            'players' => $this->getPlayerData(),
            'winner' => null
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
            'deck' => $this->dealer->getDeck(),
            'pot' => $this->hand->fresh()->pot->fresh()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players' => $this->getPlayerData(),
            'winner' => null
        ];
    }

    protected function updatePlayerStatusesOnNewStreet()
    {
        /*
         * Reset can_continue & BB status once pre-flop action and/or previous street is finished.
         */
        TableSeat::query()
            ->where('table_id', $this->handTable->fresh()->id)
            ->update([
                'can_continue' => 0
            ]);

        /*
         * Always reset action_id.
         */
        PlayerAction::query()
            ->where('hand_id', $this->hand->fresh()->id)
            ->update([
                'action_id' => null
            ]);
    }

    protected function readyForShowdown()
    {
        return $this->hand->fresh()->streets->count() === count($this->game->streets) &&
            $this->hand->fresh()->playerActions->where('active', 1)->count() ===
            $this->handTable->fresh()->tableSeats->where('can_continue', 1)->count();
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return $this->hand->fresh()->playerActions->where('active', 1)->count()
            === $this->handTable->fresh()->tableSeats->where('can_continue', 1)->count()
            && $this->handTable->fresh()->tableSeats->where('can_continue', 1)->count() === 1;
    }

    protected function allActivePlayersCanContinue()
    {
        return $this->hand->fresh()->playerActions->fresh()->where('active', 1)->count() ===
            $this->handTable->fresh()->tableSeats->fresh()->where('can_continue', 1)->count();
    }

    protected function theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()
    {
        return $this->hand->fresh()->playerActions->fresh()->where('active', 1)->where('big_blind', 1)->count() === 1
            && !$this->hand->fresh()->playerActions->fresh()->where('active', 1)->where('big_blind', 0)->first();
    }

    protected function theLastHandWasCompleted()
    {
        return $this->hand->fresh()->completed_on;
    }

    protected function allPlayerActionsAreNullSoANewSreetHasBeenSet()
    {
        return !$this->hand->playerActions->fresh()->whereNotNull('action_id')->first();
    }

    protected function getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer)
    {

        $dealer = $this->hand->fresh()
            ->playerActions
            ->where('table_seat_id', TableSeat::where('is_dealer', 1)->first()->fresh()->id)
            ->first()
            ->fresh()
            ->tableSeat->fresh();

        $dealerIsActive = $dealer->active ? $dealer : false;

        if($dealerIsActive){

            if($firstActivePlayer->is_dealer){

                $playerAfterDealer = $this->hand->playerActions
                    ->fresh()
                    ->where('active', 1)
                    ->where('table_seat_id', '>', $firstActivePlayer->id)
                    ->first()
                    ->tableSeat;

                $firstActivePlayer = $playerAfterDealer ?: $this->hand->playerActions
                    ->fresh()
                    ->where('active', 1)
                    ->where('table_seat_id', '!=', $firstActivePlayer->table_seat_id)
                    ->first()
                    ->tableSeat;

            } else if($firstActivePlayer->id < $dealerIsActive->id){

                $playerAfterDealer = $this->hand->playerActions
                    ->fresh()
                    ->where('active', 1)
                    ->where('table_seat_id', '>', $dealerIsActive->id)
                    ->first()
                    ->tableSeat;

                $firstActivePlayer = $playerAfterDealer ?: $firstActivePlayer;

            }

        } else {

            $playerAfterDealer = $this->hand->playerActions
                ->fresh()
                ->where('active', 1)
                ->where('table_seat_id', '>', $dealer->id)
                ->first();

            $firstActivePlayer = $playerAfterDealer ? $playerAfterDealer->tableSeat : $firstActivePlayer;

        }

        return $firstActivePlayer;
    }

    public function getActionOn()
    {

        $firstActivePlayer = TableSeat::query()
            ->select('table_seats.*')
            ->leftJoin('player_actions', 'table_seats.id', '=', 'player_actions.table_seat_id')
            ->where('table_seats.table_id', $this->handTable->fresh()->id)
            ->where('table_seats.id', $this->hand->fresh()->playerActions->where('active', 1)->first()->table_seat_id)
            ->first()
            ->fresh();

        if($this->allPlayerActionsAreNullSoANewSreetHasBeenSet()){
            return $this->getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer);
        }

        $lastToAct = $this->hand->playerActions
            ->fresh()
            ->sortBy([
                ['updated_at', 'desc']
            ], SORT_NUMERIC)
            ->first()
            ->table_seat_id;

        $playerAfterLastToAct = $this->hand->playerActions->fresh()->where('active', 1)->where('table_seat_id', '>', $lastToAct)->first()
            ? $this->hand->playerActions->fresh()->where('active', 1)->where('table_seat_id', '>', $lastToAct)->first()->tableSeat
            : null;

        if(!$playerAfterLastToAct){
            return $firstActivePlayer;
        }

        return $playerAfterLastToAct;

    }

    protected function getPlayerData()
    {

        $playerData = [];

        foreach($this->hand->playerActions->fresh() as $playerAction){

            $actionOn = false;

            if($this->getActionOn() && $this->getActionOn()->player_id === $playerAction->player_id){
                $actionOn = true;
            }

            $actionName = $playerAction->action ? $playerAction->action->name : null;

            $stack = $playerAction->player->fresh()->stacks->where('table_id', $this->handTable->id)->first()
                ? $playerAction->player->fresh()->stacks->where('table_id', $this->handTable->id)->first()->amount
                : null;

            $playerData[] = [
                'stack' => $stack,
                'name' => $playerAction->player->name,
                'action_id' => $playerAction->action_id,
                'action_name' => $actionName ,
                'player_id' => $playerAction->player_id,
                'table_seat_id' =>  $playerAction->table_seat_id,
                'hand_street_id' => $playerAction->hand_street_id,
                'bet_amount' => $playerAction->bet_amount,
                'active' => $playerAction->active,
                'can_continue' => $playerAction->tableSeat->can_continue,
                'is_dealer' => $playerAction->tableSeat->is_dealer,
                'big_blind' => $playerAction->big_blind,
                'small_blind' => $playerAction->small_blind,
                'whole_cards' => $this->getWholeCards($playerAction->player),
                'action_on' => $actionOn,
                'availableOptions' => $this->getAvailableOptionsBasedOnLatestAction($playerAction)
            ];
        }

        return $playerData;
    }

    public function getWholeCards($player = null)
    {

        $wholeCards = [];

        if(isset($player)){
            foreach($player->wholeCards->where('hand_id', $this->hand->fresh()->id) as $wholeCard){
                $wholeCards[] = [
                    'player_id' => $wholeCard->player_id,
                    'rank' => $wholeCard->card->rank->abbreviation,
                    'suit' => $wholeCard->card->suit->name,
                    'suitAbbreviation' => $wholeCard->card->suit->abbreviation
                ];
            }

            return $wholeCards;
        }

        foreach(TableSeat::where('can_continue', 1)->get() as $tableSeat){
            foreach($tableSeat->player->wholeCards->where('hand_id',$this->hand->fresh()->id) as $wholeCard){
                $wholeCards[] = [
                    'player_id' => $tableSeat->player->id,
                    'rank' => $wholeCard->card->rank->abbreviation,
                    'suit' => $wholeCard->card->suit->name,
                    'suitAbbreviation' => $wholeCard->card->suit->abbreviation
                ];
            }
        }

        return $wholeCards;
    }

    public function getCommunityCards()
    {
        $cards = [];
        foreach($this->hand->fresh()->streets as $street){
            foreach($street->cards as $streetCard){
                $cards[] = [
                    'rank' => $streetCard->card->rank->abbreviation,
                    'suit' => $streetCard->card->suit->name,
                    'suitAbbreviation' => $streetCard->card->suit->abbreviation
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
        $latestAction = $this->hand->playerActions
            ->fresh()
            ->whereNotIn('action_id', [
                $this->fold->id
            ])
            ->sortBy([
                ['updated_at', 'desc']
            ], SORT_NUMERIC)
            ->first();

        if($playerAction->active === 1){

            $options = [
                $this->fold
            ];

            switch($latestAction->action_id){
                case $this->call->id:
                    /*
                     * BB can only check if there were no raises before the latest call action.
                     */
                    if(
                        $playerAction->big_blind === 1 &&
                        !$this->hand->playerActions->fresh()->whereIn('action_id', $this->raise->id)->first()
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

        return collect($options);
    }

    public function updateAllOtherSeatsBasedOnLatestAction()
    {

        $latestAction = $this->hand->playerActions
            ->fresh()
            ->sortBy([
                ['updated_at', 'desc']
            ], SORT_NUMERIC)
            ->first();

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
            TableSeat::query()
                ->where('table_id', $this->handTable->id)
                ->where('id', '!=', $latestAction->table_seat_id)
                ->update([
                    'can_continue' => $canContinue
                ]);
        }

    }

    public function updateSeatStatusOfLatestAction()
    {

        $latestAction = $this->hand->playerActions
            ->fresh()
            ->sortBy([
                ['updated_at', 'desc']
            ], SORT_NUMERIC)
            ->first();

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

        TableSeat::where('id', $latestAction->table_seat_id)
            ->update([
                'can_continue' => $canContinue
            ]);
    }

    public function initiateStreetActions()
    {
        $this->street = HandStreet::create([
            'street_id' => Street::where('name', 'Pre-flop')->first()->id,
            'hand_id' => $this->hand->id
        ]);

        foreach($this->handTable->tableSeats as $seat){
            $seat->player->actions()->create([
                'hand_street_id' => $this->street->id,
                'table_seat_id' => $seat->id,
                'hand_id' => $this->hand->id,
                'active' => 1
            ]);

            PlayerAction::where([
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

        foreach($this->handTable->tableSeats as $tableSeat){
            if($tableSeat->player->stacks->count() === 0){
                $tableSeat->player->stacks()->create([
                    'amount' => 1000,
                    'table_id' => $this->handTable->id
                ]);
            }
        }

        return $this;
    }

    protected function noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)
    {
        return !$currentDealer || !$this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 1)->first();
    }

    protected function thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 3)->first();
    }

    protected function thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 2)->first() &&
            !$this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 3)->first();
    }

    protected function thereIsOneSeatAfterTheDealer($currentDealer)
    {
        return $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 1)->first()
            && !$this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 2)->first();
    }

    protected function identifyTheNextDealerAndBlindSeats($currentDealer)
    {

        if($currentDealer){
            $currentDealer = $this->handTable->tableSeats->where('id', $currentDealer)->first();
        } else {
            $currentDealer = $this->handTable->tableSeats->where('is_dealer', 1)->first();
        }

        if($this->noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)){

            $dealer = $this->handTable->tableSeats->fresh()->slice(0, 1)->first();
            $smallBlindSeat = $this->handTable->tableSeats->fresh()->where('id', $dealer->id + 1)->first();
            $bigBlindSeat = $this->handTable->tableSeats->fresh()->where('id', $dealer->id + 2)->first();

        } else if($this->thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer = $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 1)->first();
            $smallBlindSeat = $this->handTable->tableSeats->fresh()->where('id', $dealer->id + 1)->first();
            $bigBlindSeat = $this->handTable->tableSeats->fresh()->where('id', $dealer->id + 2)->first();

        } else if($this->thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer = $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 1)->first();
            $smallBlindSeat = $this->handTable->tableSeats->fresh()->where('id', $dealer->id + 1)->first();
            $bigBlindSeat = $this->handTable->tableSeats->fresh()->slice(0, 1)->first();

        } else {

            $dealer = $this->handTable->tableSeats->fresh()->where('id', $currentDealer->id + 1)->first();
            $smallBlindSeat = $this->handTable->tableSeats->fresh()->slice(0, 1)->first();
            $bigBlindSeat = $this->handTable->tableSeats->fresh()->slice(1, 1)->first();

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

        if($this->hand->fresh()->streets->count() === 1){
            PlayerAction::query()
                ->where('hand_id', $this->hand->fresh()->id)
                ->where('big_blind', 1)
                ->update([
                    'big_blind' => 0
                ]);
        }

        [
            'currentDealer' => $currentDealer,
            'dealer' => $dealer,
            'smallBlindSeat' => $smallBlindSeat,
            'bigBlindSeat' => $bigBlindSeat
        ] = $this->identifyTheNextDealerAndBlindSeats($currentDealer);

        if($currentDealer){
            TableSeat::query()
                ->where('table_id', $this->handTable->id)
                ->where('id', '=',  $currentDealer->id)
                ->update([
                    'is_dealer' => 0,
                    'updated_at' => date('Y-m-d H:i:s', strtotime('- 20 seconds'))
                ]);
        }

        TableSeat::query()
            ->where('table_id', $this->handTable->id)
            ->where('id', '=',  $dealer->id)
            ->update([
                'is_dealer' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('- 18 seconds'))
            ]);

        $smallBlind = PlayerAction::where([
            'player_id' =>  $smallBlindSeat->player->id,
            'table_seat_id' =>  $smallBlindSeat->id,
            'hand_street_id' => HandStreet::where([
                'street_id' => Street::where('name', $this->game->streets[0]['name'])->first()->id,
                'hand_id' => $this->hand->id
            ])->first()->id
        ])->first();

        $bigBlind = PlayerAction::where([
            'player_id' =>  $bigBlindSeat->player->id,
            'table_seat_id' =>  $bigBlindSeat->id,
            'hand_street_id' => HandStreet::where([
                'street_id' => Street::where('name', $this->game->streets[0]['name'])->first()->id,
                'hand_id' => $this->hand->id
            ])->first()->id
        ])->first();

        BetHelper::postBlinds($this->hand, $smallBlind, $bigBlind);

    }

}
