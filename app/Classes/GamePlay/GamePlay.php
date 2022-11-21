<?php

namespace App\Classes\GamePlay;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\PotLimitHoldEm;
use App\Classes\GameState\GameState;
use App\Classes\Showdown\Showdown;
use App\Helpers\BetHelper;
use App\Helpers\PotHelper;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\TableSeat;
use App\Constants\Action;
use App\Models\Stack;

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
    private ?GameState $gameState;
    private bool $newStreet = false;

    public function __construct($hand, $deck = null)
    {
        $this->game      = new PotLimitHoldEm();
        $this->dealer    = (new Dealer())->setDeck($deck);
        $this->hand      = $hand;
        $this->handTable = $hand->table();
    }

    /**
     * To assist with unit test scenario set-up.
     */
    public function setGameState(GameState $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function play(GameState $gameState = null)
    {
        $this->gameState = $gameState;

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
        $street = HandStreet::create([
            'street_id' => Street::find(['name' => $this->game->streets[$this->gameState->handStreetCount()]['name']])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->dealer->dealStreetCards(
            $street,
            $this->game->streets[$this->gameState->incrementedHandStreets() - 1]['community_cards']
        );

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->hand->pot()->amount,
            'communityCards' => $this->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => null
        ];
    }

    public function start(
        $currentDealer = null,
        GameState $gameState = null
    ) {
        $this->gameState = $gameState;

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
            TableSeat::bigBlindWins($this->gameState->handId());

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
        TableSeat::find(['table_id' => $this->gameState->tableId()])
            ->updateBatch([
                'can_continue' => 0
            ], 'table_id = ' . $this->gameState->tableId());

        PlayerAction::find(['hand_id' => $this->gameState->handId()])
            ->updateBatch([
                'action_id' => null
            ], 'hand_id = ' . $this->gameState->handId());

        /**
         * Added flag for new street as preceeding action
         * updates were not being picked up when setting
         * action options.
         */
        $this->newStreet = true;
    }

    protected function readyForShowdown()
    {
        return count($this->gameState->getUpdatedHandStreets()->content) === count($this->game->streets) &&
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
            'hand_id'   => $this->gameState->handId()
        ])->content;

        $nonBigBlindActive = PlayerAction::find([
            'active'    => 1,
            'big_blind' => 0,
            'hand_id'   => $this->gameState->handId()
        ])->content;

        return count($bigBlindActive) === 1 && count($nonBigBlindActive) === 0;
    }

    protected function theLastHandWasCompleted()
    {
        return $this->hand->completed_on;
    }

    protected function getThePlayerActionShouldBeOnForANewStreet(TableSeat $firstActivePlayer)
    {
        $dealer = $this->hand->getDealer();

        $playerAfterDealer = TableSeat::playerAfterDealer(
            $this->gameState->handId(),
            $dealer->table_seat_id
        );

        if (!isset($playerAfterDealer->player_id)) {
            $playerAfterDealer = null;
        }

        return $playerAfterDealer ?: $firstActivePlayer;
    }

    public function getActionOn(): TableSeat
    {
        $firstActivePlayer = TableSeat::firstActivePlayer($this->gameState->handId());

        if ($this->newStreet) {
            return $this->getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer);
        }

        $lastToAct = $this->gameState->getLatestAction()->table_seat_id;

        $activePlayersAfterLastToAct = array_filter(
            PlayerAction::find(['active' => 1, 'hand_id' => $this->gameState->handId()])->collect()->content,
            function ($value) use ($lastToAct) {
                return $value->table_seat_id > $lastToAct;
            }
        );

        $playerAfterLastToAct = count($activePlayersAfterLastToAct)
            ? array_shift($activePlayersAfterLastToAct)
            : null;

        if (!$playerAfterLastToAct) {
            return $firstActivePlayer;
        }

        return $playerAfterLastToAct->tableSeat();
    }

    protected function getPlayerData()
    {
        $playerData  = [];
        $actionOnGet = $this->getActionOn();

        foreach(PlayerAction::find(['hand_id' => $this->gameState->handId()])->collect()->content as $playerAction){
            $actionOn = false;

            if($actionOnGet && $actionOnGet->player_id === $playerAction->player_id){
                $actionOn = true;
            }

            $actionName = $playerAction->action_id ? $playerAction->action()->name : null;

            $stack = $playerAction->player()->stacks()->search('table_id', $this->gameState->tableId())
                ? $playerAction->player()->stacks()->search('table_id', $this->gameState->tableId())->amount
                : null;

            $playerData[] = [
                'stack'            => $stack,
                'name'             => $playerAction->player()->name,
                'action_id'        => $playerAction->action_id,
                'action_name'      => $actionName,
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
                'availableOptions' => $actionOn ? $this->getAvailableOptionsBasedOnLatestAction($playerAction) : []
            ];
        }

        return $playerData;
    }

    public function getWholeCards($player = null)
    {
        $wholeCards = [];

        if(isset($player)){
            foreach($player->getWholeCards($this->gameState->handId()) as $wholeCard){
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
        foreach($this->gameState->getUpdatedHandStreets()->collect()->content as $street){
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
        if ($this->newStreet) {
            return [Action::FOLD, Action::CHECK, Action::BET];
        }

        $latestAction = $this->gameState->getLatestAction();

        /**
         * BB is the only player that can fold / check / raise pre-flop
         */
        if (count($this->gameState->getUpdatedHandStreets()->content) === 1 && !$playerAction->big_blind) {
            return [Action::FOLD, Action::CALL, Action::RAISE];
        }

        switch($latestAction->action_id){
            case Action::CALL['id']:
                /**
                 * BB can only check if there were no raises before the latest call action.
                 */
                if ($playerAction->big_blind && !$this->hand->actions()->search('action_id', Action::RAISE['id'])) {
                    return [Action::FOLD, Action::CHECK, Action::RAISE];
                } else {
                    return [Action::FOLD, Action::CALL, Action::RAISE];
                }
                break;
            case Action::BET['id']:
            case Action::RAISE['id']:
                return [Action::FOLD, Action::CALL, Action::RAISE];
                break;
            case Action::CHECK['id']:
                return [Action::FOLD, Action::CHECK, Action::BET];
                break;
            default:
                /**
                 * The latest action may be a fold, so we need
                 * to check if anyone has raised, called or bet
                 * before the folder.
                 */
                 // Need this condition?: !in_array($playerAction->player_id, array_column($continuingBetters, 'player_id'))
                $continuingBetters = TableSeat::getContinuingBetters($this->gameState->getHand()->id);

                if (0 < count($continuingBetters)) {
                    return [Action::FOLD, Action::CALL, Action::RAISE];
                    break;
                }

                return [Action::FOLD, Action::CHECK, Action::BET];
                break;
        }
    }

    public function initiateStreetActions()
    {
        $street = HandStreet::create([
            'street_id' => Street::find(['name' => 'Pre-flop'])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        foreach($this->gameState->getSeats() as $seat){
            PlayerAction::create([
                'player_id'      => $seat['player_id'],
                'hand_street_id' => $street->id,
                'table_seat_id'  => $seat['id'],
                'hand_id'        => $this->gameState->handId(),
                'active'         => 1
            ]);
        }

        return $this;
    }

    public function initiatePlayerStacks()
    {
        foreach($this->gameState->getSeats() as $seat){
            /**
             * Looks like this count() check was added
             * as there's only 1 table being handled.
             */
            $tableStacks = Stack::find([
                'player_id' => $seat['player_id'],
                'table_id' => $this->gameState->tableId()
            ]);

            if(count($tableStacks->content) === 0){
                Stack::create([
                    'amount' => 1000,
                    'player_id' => $seat['player_id'],
                    'table_id' => $this->gameState->tableId()
                ]);
            }
        }

        return $this;
    }

    protected function noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)
    {
        return !$currentDealer || !$this->gameState->getSeat($currentDealer['id'] + 1);
    }

    protected function thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->gameState->getSeat($currentDealer['id'] + 3);
    }

    protected function thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)
    {
        return $this->gameState->getSeat($currentDealer['id'] + 2);
    }

    protected function thereIsOneSeatAfterTheDealer($currentDealer)
    {
        return $this->gameState->getSeat($currentDealer['id'] + 1);
    }

    protected function identifyTheNextDealerAndBlindSeats($currentDealer)
    {
        if($currentDealer){
            $currentDealer = $this->gameState->getSeat($currentDealer->id);
        } else {
            $currentDealer = $this->gameState->getDealer();
        }

        /**
         * TODO: these methods must currently be called
         * in order. Consider changing. Also will only
         * work if all seats have a stack/player.
         */
        if($this->noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)){
            $dealer         = $this->gameState->getSeats()[0];
            $smallBlindSeat = $this->gameState->getSeat($dealer['id'] + 1);
            $bigBlindSeat   = $this->gameState->getSeat($dealer['id'] + 2);

        } else if($this->thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->gameState->getSeat($currentDealer['id'] + 1);
            $smallBlindSeat = $this->gameState->getSeat($dealer['id'] + 1);
            $bigBlindSeat   = $this->gameState->getSeat($dealer['id'] + 2);

        } else if($this->thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->gameState->getSeat($currentDealer['id'] + 1);
            $smallBlindSeat = $this->gameState->getSeat($dealer['id'] + 1);
            $bigBlindSeat   = $this->gameState->getSeats()[0];

        } else {

            $dealer         = $this->gameState->getSeat($currentDealer['id'] + 1);
            $smallBlindSeat = $this->gameState->getSeats()[0];
            $bigBlindSeat   = $this->gameState->getSeats()[1];

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
        if($this->gameState->handStreetCount() === 1){
            $bigBlind = PlayerAction::find([
                'hand_id' => $this->gameState->handId(),
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
            $currentDealerSeat = TableSeat::find([
                'id'       =>  $currentDealer['id'],
                'table_id' => $this->gameState->tableId()
            ]);

            $currentDealerSeat->update([
                'is_dealer'  => 0
            ]);
        }

        $newDealerSeat = TableSeat::find([
            'id'       =>  $dealer['id'],
            'table_id' => $this->gameState->tableId()
        ]);

        $newDealerSeat->update([
            'is_dealer'  => 1
        ]);

        $handStreetId = HandStreet::find([
            'street_id'  => Street::find(['name' => $this->game->streets[0]['name']])->id,
            'hand_id' => $this->gameState->handId()
        ])->id;

        $smallBlind = PlayerAction::find([
            'player_id'      => $smallBlindSeat['player_id'],
            'table_seat_id'  => $smallBlindSeat['id'],
            'hand_street_id' => $handStreetId,
        ]);

        $bigBlind = PlayerAction::find([
            'player_id'      => $bigBlindSeat['player_id'],
            'table_seat_id'  => $bigBlindSeat['id'],
            'hand_street_id' => $handStreetId,
        ]);
        
        $this->gameState->setLatestAction($bigBlind);

        BetHelper::postBlinds($this->hand, $smallBlind, $bigBlind);
    }
}
