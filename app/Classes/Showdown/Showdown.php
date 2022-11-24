<?php

namespace App\Classes\Showdown;

use App\Classes\GameState\GameState;
use App\Classes\HandIdentifier\HandIdentifier;
use App\Models\Player;

class Showdown
{
    public HandIdentifier $handIdentifier;
    public GameState $gameState;
    public $winner;

    /**
     * @var array<Card>
     */
    public $communityCards = [];

    /**
     * @var array<mixed>
     */
    public array $playerHands = [];

    /**
     * @param GameState $gameState
     */
    public function __construct($gameState)
    {
        $this->handIdentifier = new HandIdentifier();
        $this->gameState      = $gameState;
    }

    public function decideWinner(): array
    {
        /*
         * foreach handType, if there are more than 1 players with that hand type,
         * retain only the one with the highest kicker & active cards as appropriate
         * then compare the hand rankings of each remaining player hand.
         */
        foreach($this->handIdentifier->handTypes as $handType){
            $playerHandsOfType = $this->getPlayerhandsOfType($handType['id']);

            if(count($playerHandsOfType) > 1){
                $this->identifyHighestRankedHandAndKickerOfThisType(
                    $this->playerHands,
                    $playerHandsOfType,
                    $handType
                );
            }
        }

        return $this->highestRankedPlayerHand();
    }

    protected function identifyHighestRankedHandAndKickerOfThisType(
        array $playerHands,
        array $playerHandsOfType,
        array $handType
    ): void {
        /**
         * Remove hands of this type from the array. That way we can only 
         * retain the highest rank/kicker-ed hand and put it back in to be 
         * compared against the other highest ranked/kicker-ed hand types.
         */
        $this->playerHands = array_filter($playerHands, function($value) use($handType){
            return $value['handType']['id'] !== $handType['id'];
        });

        $handsOfThisTypeRanked = $this->getBestHandByHighestActiveCardRank(
            $playerHandsOfType
        );

        if(count($handsOfThisTypeRanked) > 1){
            /*
             * TODO: split pots, this functionality is currently
             * set to only return the first one even if multiple players
             * share the same best active cards and kickers.
             */
            $handsOfThisTypeRanked = $this->getBestHandByHighestKicker(
                $playerHandsOfType
            );
        }

        $highestRankedHandOfType = $handsOfThisTypeRanked[array_key_first($handsOfThisTypeRanked)];

        array_push($this->playerHands, $highestRankedHandOfType);
    }

    public function compileHands(): self
    {
        $this->getCommunityCards();

        foreach ($this->getContinuingPlayerSeats($this->gameState->getPlayers()) as $player) {
            $wholeCards = [];

            foreach (Player::getWholeCards($this->gameState->handId(), $player['player_id']) as $wholeCard) {
                $wholeCards[] = $wholeCard;
            }

            $compileInfo = (new HandIdentifier())->identify($wholeCards, $this->communityCards)->identifiedHandType;
            $compileInfo['highestActiveCard'] = max($compileInfo['activeCards']);
            $compileInfo['player']            = $player;

            $this->playerHands[] = $compileInfo;
        }

        return $this;
    }

    private function getContinuingPlayerSeats(array $players): array {
        return array_filter($players, function($player) {
            return 1 === $player['active'] && 1 === $player['can_continue'];
        });
    }

    public function getCommunityCards(): void
    {
        foreach($this->gameState->getHandStreets()->collect()->content as $handStreet){
            foreach($handStreet->cards()->collect()->content as $handStreetCard){
                $this->communityCards[] = $handStreetCard->getCard();
            }
        }
    }

    /**
     * @param int $handTypeId
     * @return array
     */
    private function getPlayerhandsOfType(int $handTypeId): array
    {
        return array_filter($this->playerHands, function($value) use($handTypeId){
            return $value['handType']['id'] == $handTypeId;
        });
    }

    /**
     * @param array $hayStack
     */
    private function getBestHandByHighestActiveCardRank(array $playerHandsOfType): array
    {
        $maxActiveCard = max(array_column($playerHandsOfType, 'highestActiveCard'));

        return array_filter($playerHandsOfType, function($value) use($maxActiveCard){
            return $value['highestActiveCard'] == $maxActiveCard;
        });
    }

    /**
     * @param array $hayStack
     */
    private function getBestHandByHighestKicker(array $playerHandsOfType): array
    {
        $maxKicker = max(array_column($playerHandsOfType, 'kicker'));

        return array_filter($playerHandsOfType, function($value) use($maxKicker){
            return $value['kicker'] == $maxKicker;
        });
    }

    /**
     * @return array
     */
    private function highestRankedPlayerHand(): array
    {
        uasort($this->playerHands, function ($a, $b){
            /**
             * Why was this if statement added? TODO
             */
            if ($a['handType']['ranking'] == $b['handType']['ranking']) {
                return 0;
            }
            return ($a['handType']['ranking'] > $b['handType']['ranking']) ? 1 : -1;
        });

        return $this->playerHands[array_key_first($this->playerHands)];
    }
}
