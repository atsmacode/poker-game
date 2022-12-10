<?php

namespace Atsmacode\PokerGame\HandStep;

use Atsmacode\PokerGame\BetHandler\BetHandler;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\PlayerAction;
use Atsmacode\PokerGame\Models\Stack;
use Atsmacode\PokerGame\Models\Street;
use Atsmacode\PokerGame\Models\TableSeat;
use Psr\Container\ContainerInterface;

/**
 * Responsible for the actions required to start a new hand.
 */
class Start extends HandStep
{
    public function __construct(
        private ContainerInterface $container,
        private Street             $streetModel,
        private HandStreet         $handStreetModel,
        private PlayerAction       $playerActionModel,
        private Stack              $stackModel,
        private TableSeat          $tableSeatModel,
        private BetHandler         $betHandler
    ) {}
    
    public function handle(GameState $gameState, TableSeat $currentDealer = null): GameState
    {
        $this->gameState = $gameState;

        $this->initiateStreetActions()->initiatePlayerStacks()->setDealerAndBlindSeats($currentDealer);
        $this->gameState->setPlayers();
        $this->gameState->getGameDealer()->setDeck()->shuffle();

        if($this->gameState->getGame()->streets[0]['whole_cards']){
            $this->gameState->getGameDealer()->dealTo(
                $this->gameState->getSeats(),
                $this->gameState->getGame()->streets[0]['whole_cards'],
                $this->gameState->getHand()->id,
            );
        }

        return $this->gameState;
    }

    public function initiateStreetActions(): self
    {
        $street = $this->handStreetModel->create(['street_id' => $this->streetModel->find(['name' => 'Pre-flop'])->id, 'hand_id' => $this->gameState->handId()]);

        foreach($this->gameState->getSeats() as $seat){
            $this->playerActionModel->create([
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
            $playerTableStack = $this->stackModel->find(['player_id' => $seat['player_id'], 'table_id'  => $this->gameState->tableId()]);

            if (0 === count($playerTableStack->content)) {
                $tableStacks[$seat['player_id']] = $this->stackModel->create([
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
            $bigBlind = $this->playerActionModel->find(['hand_id' => $this->gameState->handId(), 'big_blind' => 1]);

            if ($bigBlind->isNotEmpty()) { $bigBlind->update(['big_blind' => 0]); }
        }

        [
            'currentDealer'      => $currentDealer,
            'dealer'             => $dealer,
            'smallBlindSeat'     => $smallBlindSeat,
            'bigBlindSeat'       => $bigBlindSeat
        ] = $this->identifyTheNextDealerAndBlindSeats($currentDealer);

        if($currentDealer){
            $currentDealerSeat = $this->tableSeatModel->find(['id' => $currentDealer['id'], 'table_id' => $this->gameState->tableId()]);
            $currentDealerSeat->update(['is_dealer'  => 0]);
        }

        $newDealerSeat = $this->tableSeatModel->find(['id' => $dealer['id'], 'table_id' => $this->gameState->tableId()]);
        $newDealerSeat->update(['is_dealer'  => 1]);

        $handStreetId = $this->handStreetModel->find([
            'street_id'  => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[0]['name']])->id,
            'hand_id' => $this->gameState->handId()
        ])->id;

        $smallBlind = $this->findPlayerAction($smallBlindSeat['player_id'], $smallBlindSeat['id'], $handStreetId); 
        $bigBlind   = $this->findPlayerAction($bigBlindSeat['player_id'], $bigBlindSeat['id'], $handStreetId); 
        
        $this->gameState->setLatestAction($bigBlind);

        $this->betHandler->postBlinds($this->gameState->getHand(), $smallBlind, $bigBlind, $this->gameState);

        return $this;
    }

    /** Needed a way to create unique instances of the model in the container */
    private function findPlayerAction(int $playerId, int $tableSeatId, int $handStreetId)
    {
        $playerActionModel = $this->container->build(PlayerAction::class);

        return $playerActionModel->find([
            'player_id'      => $playerId,
            'table_seat_id'  => $tableSeatId,
            'hand_street_id' => $handStreetId,
        ]);
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

        //var_dump($this->gameState->getSeats());

        return [
            'currentDealer'  => $currentDealer,
            'dealer'         => $dealer,
            'smallBlindSeat' => $smallBlindSeat,
            'bigBlindSeat'   => $bigBlindSeat
        ];
    }
}
