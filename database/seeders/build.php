<?php

$migrations = [
    'drop_database',
    'create_database',
    'create_ranks_table',
    'create_suits_table',
    'create_cards_table'
];

$seeders = [
    'seed_ranks_table',
    'seed_suits_table',
    'seed_cards_table'
];

foreach($migrations as $migration){
    require('../migrations/'.$migration.'.php');
}

foreach($seeders as $seeder){
    require($seeder.'.php');
}

