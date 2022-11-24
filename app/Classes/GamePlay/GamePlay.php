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
    public  $game;
    public  $dealer;
    private ?GameState $gameState;

    public function __construct($deck = null)
    {
        $this->game   = new PotLimitHoldEm();
        $this->dealer = (new Dealer())->setDeck($deck);
    }

    public function setGameState(GameState $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function response(array $winner = null): array
    {
        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->gameState->getPot(),
            'communityCards' => $this->gameState->setCommunityCards()->getCommunityCards(),
            'players'        => $this->getPlayerData(),
            'winner'         => $winner
        ];
    }

    public function nextStep()
    {
        if ($this->theLastHandWasCompleted()) { return $this->start(); }

        $this->gameState->setPlayers();

        if ($this->theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()) {
            TableSeat::bigBlindWins($this->gameState->handId());

            return $this->showdown();
        }

        if ($this->readyForShowdown() || $this->onePlayerRemainsThatCanContinue()) { return $this->showdown(); }

        if ($this->allActivePlayersCanContinue()) { return $this->continue(); }

        return $this->response();
    }

    public function start(GameState $gameState = null, $currentDealer = null) {
        $this->gameState = $gameState;

        $this->initiateStreetActions()->initiatePlayerStacks()->setDealerAndBlindSeats($currentDealer);
        $this->gameState->setPlayers();
        $this->dealer->setDeck()->shuffle();

        if($this->game->streets[0]['whole_cards']){
            $this->dealer->dealTo(
                $this->gameState->getSeats(),
                $this->game->streets[0]['whole_cards'],
                $this->gameState->getHand(),
            );
        }

        return $this->response();
    }

    public function play(GameState $gameState)
    {
        $this->gameState = $gameState;

        return $this->nextStep();
    }

    public function continue()
    {
        $this->updatePlayerStatusesOnNewStreet();

        $street = HandStreet::create([
            'street_id' => Street::find(['name' => $this->game->streets[$this->gameState->handStreetCount()]['name']])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->dealer->dealStreetCards(
            $street,
            $this->game->streets[$this->gameState->incrementedHandStreets() - 1]['community_cards']
        );

        $this->gameState->updateHandStreets();

        return $this->response();
    }

    public function showdown()
    {
        $this->gameState->setPlayers();

        $winner = (new Showdown($this->gameState))->compileHands()->decideWinner();

        PotHelper::awardPot(
            $winner['player']['stack'],
            $this->gameState->getPot(),
            $winner['player']['player_id'],
            $winner['player']['table_id']
        );

        $this->gameState->getHand()->complete();

        return $this->response($winner);
    }

    protected function readyForShowdown()
    {
        return count($this->gameState->getHandStreets()->content) === count($this->game->streets) &&
            count($this->gameState->getActivePlayers()) ===
            count($this->gameState->getContinuingPlayers());
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return count($this->gameState->getActivePlayers())
            === count($this->gameState->getContinuingPlayers())
            && count($this->gameState->getContinuingPlayers()) === 1;
    }

    protected function allActivePlayersCanContinue()
    {
        return count($this->gameState->getActivePlayers()) === count($this->gameState->getContinuingPlayers());
    }

    protected function theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()
    {
        $activePlayers = array_values(array_filter($this->gameState->getPlayers(), function($player){
            return 1 === $player['active'];
        }));

        return 1 === count($activePlayers) && 1 === $activePlayers[0]['big_blind'];
    }

    protected function theLastHandWasCompleted()
    {
        return $this->gameState->getHand()->completed_on;
    }

    protected function getThePlayerActionShouldBeOnForANewStreet(array $firstActivePlayer)
    {
        $dealer            = $this->gameState->getHand()->getDealer();
        $playerAfterDealer = TableSeat::playerAfterDealer($this->gameState->handId(), $dealer->table_seat_id);

        if (!isset($playerAfterDealer->player_id)) { $playerAfterDealer = null; }

        return $playerAfterDealer->content[0] ?: $firstActivePlayer;
    }

    public function getActionOn()
    {
        $firstActivePlayer = $this->gameState->firstActivePlayer();
        $lastToAct         = $this->gameState->getLatestAction()->table_seat_id;

        if ($this->gameState->isNewStreet()) {
            return $this->getThePlayerActionShouldBeOnForANewStreet($firstActivePlayer);
        }

        $activePlayersAfterLastToAct = array_filter($this->gameState->getActivePlayers(), function ($value) use ($lastToAct) {
                return $value['table_seat_id'] > $lastToAct;
        });

        $playerAfterLastToAct = count($activePlayersAfterLastToAct) ? array_shift($activePlayersAfterLastToAct) : null;

        return $playerAfterLastToAct ?: $firstActivePlayer;
    }

    protected function getPlayerData()
    {
        $playerData  = []; $actionOnGet = $this->getActionOn();

        $this->gameState->setWholeCards();

        foreach($this->gameState->getPlayers() as $playerAction){
            $actionOn   = $actionOnGet && $actionOnGet['player_id'] === $playerAction['player_id'] ? true : false;
            $actionName = $playerAction['action_id'] ? $playerAction['actionName'] : null;
            $stack      = $playerAction['stack'];
            $wholeCards = isset($this->gameState->getWholeCards()[$playerAction['player_id']]) 
                ? $this->gameState->getWholeCards()[$playerAction['player_id']]
                : [];

            $playerData[] = [
                'stack'            => $stack ?? null,
                'name'             => $playerAction['playerName'],
                'action_id'        => $playerAction['action_id'],
                'action_name'      => $actionName,
                'player_id'        => $playerAction['player_id'],
                'table_seat_id'    => $playerAction['table_seat_id'],
                'hand_street_id'   => $playerAction['hand_street_id'],
                'bet_amount'       => $playerAction['bet_amount'],
                'active'           => $playerAction['active'],
                'can_continue'     => $playerAction['can_continue'],
                'is_dealer'        => $playerAction['is_dealer'],
                'big_blind'        => $playerAction['big_blind'],
                'small_blind'      => $playerAction['small_blind'],
                'whole_cards'      => $wholeCards,
                'action_on'        => $actionOn,
                'availableOptions' => $actionOn ? $this->getAvailableOptionsBasedOnLatestAction($playerAction) : []
            ];
        }

        return $playerData;
    }

    public function getAvailableOptionsBasedOnLatestAction($playerAction)
    {
        if ($this->gameState->isNewStreet()) { return [Action::FOLD, Action::CHECK, Action::BET]; }

        /** BB is the only player that can fold / check / raise pre-flop */
        if (count($this->gameState->getHandStreets()->content) === 1 && !$playerAction['big_blind']) {
            return [Action::FOLD, Action::CALL, Action::RAISE];
        }

        $latestAction      = $this->gameState->getLatestAction();
        $continuingBetters = TableSeat::getContinuingBetters($this->gameState->getHand()->id);

        switch($latestAction->action_id){
            case Action::CALL['id']:
                /** BB can only check if there were no raises before the latest call action. */
                if ($playerAction['big_blind'] && !$this->gameState->getHand()->actions()->search('action_id', Action::RAISE['id'])) {
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
                /** Latest action may be a fold, so we need to check for raisers/callers/bettters before the folder. */
                if (0 < count($continuingBetters)) { return [Action::FOLD, Action::CALL, Action::RAISE]; break; }

                return [Action::FOLD, Action::CHECK, Action::BET];
                break;
        }
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

    protected function identifyTheNextDealerAndBlindSeats($currentDealerSet): array
    {
        $currentDealer = $currentDealerSet 
            ? $this->gameState->getSeat($currentDealerSet->id) 
            : $this->gameState->getDealer();

        /** TODO: These must be called in order. Also will only work if all seats have a stack/player.*/
        if ($this->noDealerIsSetOrThereIsNoSeatAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->gameState->getSeats()[0];
            $smallBlindSeat = $this->gameState->getSeat($dealer['id'] + 1);
            $bigBlindSeat   = $this->gameState->getSeat($dealer['id'] + 2);

        } else if ($this->thereAreThreeSeatsAfterTheCurrentDealer($currentDealer)) {

            $dealer         = $this->gameState->getSeat($currentDealer['id'] + 1);
            $smallBlindSeat = $this->gameState->getSeat($dealer['id'] + 1);
            $bigBlindSeat   = $this->gameState->getSeat($dealer['id'] + 2);

        } else if ($this->thereAreTwoSeatsAfterTheCurrentDealer($currentDealer)) {

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

        $this->gameState->setNewStreet();
    }

    public function initiateStreetActions()
    {
        $street = HandStreet::create(['street_id' => Street::find(['name' => 'Pre-flop'])->id, 'hand_id' => $this->gameState->handId()]);

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
        $tableStacks = [];

        foreach($this->gameState->getSeats() as $seat){
            /** Looks like the count() check was added as there's only 1 table being handled. */
            $playerTableStack = Stack::find(['player_id' => $seat['player_id'], 'table_id'  => $this->gameState->tableId()]);

            if (0 === count($playerTableStack->content)) {
                $tableStacks[$seat['player_id']] = Stack::create([
                    'amount' => 1000,
                    'player_id' => $seat['player_id'],
                    'table_id' => $this->gameState->tableId()
                ]);
            } else {
                $tableStacks[$seat['player_id']] = $playerTableStack;
            }
        }

        $this->gameState->setStacks($tableStacks);

        return $this;
    }

    public function setDealerAndBlindSeats($currentDealer = null)
    {
        if($this->gameState->handStreetCount() === 1){
            $bigBlind = PlayerAction::find(['hand_id' => $this->gameState->handId(), 'big_blind' => 1]);

            if ($bigBlind->isNotEmpty()) { $bigBlind->update(['big_blind' => 0]); }
        }

        [
            'currentDealer'      => $currentDealer,
            'dealer'             => $dealer,
            'smallBlindSeat'     => $smallBlindSeat,
            'bigBlindSeat'       => $bigBlindSeat
        ] = $this->identifyTheNextDealerAndBlindSeats($currentDealer);

        if($currentDealer){
            $currentDealerSeat = TableSeat::find(['id' => $currentDealer['id'], 'table_id' => $this->gameState->tableId()]);
            $currentDealerSeat->update(['is_dealer'  => 0]);
        }

        $newDealerSeat = TableSeat::find(['id' => $dealer['id'], 'table_id' => $this->gameState->tableId()]);
        $newDealerSeat->update(['is_dealer'  => 1]);

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

        BetHelper::postBlinds($this->gameState->getHand(), $smallBlind, $bigBlind, $this->gameState);
    }
}
