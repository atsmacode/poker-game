# Commands

Configs:

You need to add db.php and db-test.php to configure your local DB credentials, like so:

<?php

return [
    'servername' => "localhost",
    'username' => "DB_USER",
    'password' => "DB_PASSWORD",
    'database' => "poker-game-test"
];

Run the unit test suite on Linux:

>dev/phpunit

Run the unit test suite on Windows:

>./runtests.bat

Drop, Create and Seed all tables. '-d true' is required to run this in test DB

> php application.php app:build-env -d true

# Endpoints

http://read-right-hands-vanilla.com/index.php/action

# Todo:
- Review Showdown kickers/rankings logic, need to retain all hand types and rank after the highest of each type is included in the array
- Sometimes no player qualifies for action-on, requires recreating specific hand(s) unit tests perhaps
- Errors during showdown - probably due to that fact it's not yet fully implemented
- look into serialization between PHP/JS - use arrays instead?
- Review how theplayer after Dealer is retrieved, I suspect it is a bit off
- Convert GamePlay class into a middleware pipeline
    - Class can be divided into different classes responsible for a different set of actions like:
        - Who goes next
        - What hand stage is next /What stage the hand is in
        - What are the available options for the next player
- Add custom join queries for relationships
    - Rather than multiple chained model calls resulting in a lot of queries - in progress, requires review of duplicated methods accrosss models - can implement gameSate object that is passed through pipeline
- Address updated_at reliance
    - Currenly manually setting these values so the expected 'action on' seat can be identified in tests
    - Add new row for each PlayerAction and use incrementing PK ID for latest action
- Review all TODO comments and implement solution
- Once everything above is tidied/finalised, add remaining unit tests from original app
- Add DB indexes, tests get slower once DB gets fuller
