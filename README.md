# Environment

## PHP

8.1.3

## MySQL

8.0.13

## Vue.Js

^3.2.39

## Node.Js

18.12.1

# Commands

## Linux
Run the unit test suite:

>dev/phpunit

Drop, Create and Seed all tables. '-d true' is required to run this in test DB:

> php dev/SymfonyApplication.php app:build-env -d true

## Windows
Run the unit test suite:

>.\dev\runtests.bat

Drop, Create and Seed all tables. '-d true' is required to run this in test DB

> php .\dev\SymfonyApplication.php app:build-env -d true

# Configs

You need to add db.php and db-test.php to configure your local DB credentials, like so:

```
<?php

return [
    'servername' => "localhost",
    'username' => "DB_USER",
    'password' => "DB_PASSWORD",
    'database' => "poker_game_test"
];
```

# Endpoints

http://read-right-hands-vanilla.com/index.php/action

# Todo:
- Review Showdown kickers/rankings logic, need to retain all hand types and rank after the highest of each type is included in the array
- Errors during showdown - probably due to that fact it's not yet fully implemented
- Add custom join queries for relationships
    - Rather than multiple chained model calls resulting in a lot of queries - in progress, requires review of duplicated methods accrosss models - can implement gameSate object that is passed through pipeline
- Review all TODO comments and implement solution
- Once everything above is tidied/finalised, add remaining unit tests from original app
- Add DB indexes, tests get slower once DB gets fuller
