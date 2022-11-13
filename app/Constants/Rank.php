<?php

namespace App\Constants;

class Rank
{
    const ACE_rank_id   = 1;
    const DEUCE_rank_id = 2;
    const THREE_rank_id = 3;
    const FOUR_rank_id  = 4;
    const FIVE_rank_id  = 5;
    const SIX_rank_id   = 6;
    const SEVEN_rank_id = 7;
    const EIGHT_rank_id = 8;
    const NINE_rank_id  = 9;
    const TEN_rank_id   = 10;
    const JACK_rank_id  = 11;
    const QUEEN_rank_id = 12;
    const KING_rank_id  = 13;

    const ACE = [
        'rank_id'           => self::ACE_rank_id,
        'rank'              => 'Ace',
        'ranking'           => 1,
        'rankAbbreviation'  => 'A',
    ];

    const DEUCE = [
        'rank_id'           => self::DEUCE_rank_id,
        'rank'              => 'Deuce',
        'ranking'           => 2,
        'rankAbbreviation'  => '2',
    ];

    const THREE = [
        'rank_id'          => self::THREE_rank_id,
        'rank'             => 'Three',
        'ranking'          => 3,
        'rankAbbreviation' => '3',
    ];

    const FOUR = [
        'rank_id'          => self::FOUR_rank_id,
        'rank'             => 'Four',
        'ranking'          => 4,
        'rankAbbreviation' => '4',
    ];

    const FIVE = [
        'rank_id'          => self::FIVE_rank_id,
        'rank'             => 'Five',
        'ranking'          => 5,
        'rankAbbreviation' => '5',
    ];

    const SIX = [
        'rank_id'          => self::SIX_rank_id,
        'rank'             => 'Six',
        'ranking'          => 6,
        'rankAbbreviation' => '6',
    ];

    const SEVEN = [
        'rank_id'          => self::SEVEN_rank_id,
        'rank'             => 'Seven',
        'ranking'          => 7,
        'rankAbbreviation' => '7',
    ];

    const EIGHT = [
        'rank_id'          => self::EIGHT_rank_id,
        'rank'             => 'Eight',
        'ranking'          => 8,
        'rankAbbreviation' => '8',
    ];

    const NINE = [
        'rank_id'          => self::NINE_rank_id,
        'rank'             => 'Nine',
        'ranking'          => 9,
        'rankAbbreviation' => '9',
    ];

    const TEN = [
        'rank_id'          => self::TEN_rank_id,
        'rank'             => 'Ten',
        'ranking'          => 10,
        'rankAbbreviation' => '10',
    ];

    const JACK = [
        'rank_id'          => self::JACK_rank_id,
        'rank'             => 'Jack',
        'ranking'          => 11,
        'rankAbbreviation' => 'J',
    ];

    const QUEEN = [
        'rank_id'          => self::QUEEN_rank_id,
        'rank'             => 'Queen',
        'ranking'          => 12,
        'rankAbbreviation' => 'Q',
    ];

    const KING = [
        'rank_id'          => self::KING_rank_id,
        'rank'             => 'King',
        'ranking'          => 13,
        'rankAbbreviation' => 'K',
    ];

    const ALL = [
        self::ACE,
        self::DEUCE,
        self::THREE,
        self::FOUR,
        self::FIVE,
        self::SIX,
        self::SEVEN,
        self::EIGHT,
        self::NINE,
        self::TEN,
        self::JACK,
        self::QUEEN,
        self::KING,
    ];
}
