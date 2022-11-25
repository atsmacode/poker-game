<?php

namespace App\Classes\HandStep;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\Game;
use App\Classes\GameState\GameState;
use App\Helpers\BetHelper;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Stack;
use App\Models\Street;
use App\Models\TableSeat;

class Start extends HandStep
{
    public function __construct(Game $game, Dealer $dealer)
    {
        $this->game   = $game;
        $this->dealer = $dealer;
    }

    public function handle(GameState $gameState, $currentDealer = null): GameState
    {
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

        return $this->gameState;
    }

    public function initiateStreetActions(): self
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

    public function initiatePlayerStacks(): self
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

    public function setDealerAndBlindSeats($currentDealer = null): self
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
}