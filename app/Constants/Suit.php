<?php

namespace App\Constants;

class Suit
{
    const CLUBS_suit_id     = 1;
    const DIAMONDS_suit_id  = 2;
    const HEARTS_suit_id    = 3;
    const SPADES_suit_id    = 4;

    const CLUBS = [
        'suit_id'          => self::CLUBS_suit_id,
        'suit'             => 'Clubs',
        'suitAbbreviation' => 'C',
    ];

    const DIAMONDS = [
        'suit_id'          => self::DIAMONDS_suit_id,
        'suit'             => 'Diamonds',
        'suitAbbreviation' => 'D',
    ];

    const HEARTS = [
        'suit_id'          => self::HEARTS_suit_id,
        'suit'             => 'Hearts',
        'suitAbbreviation' => 'H',
    ];

    const SPADES = [
        'suit_id'          => self::SPADES_suit_id,
        'suit'             => 'Spades',
        'suitAbbreviation' => 'S',
    ];

    const ALL = [
        self::CLUBS,
        self::DIAMONDS,
        self::HEARTS,
        self::SPADES,
    ];
}
