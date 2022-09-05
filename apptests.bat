php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox tests/Unit/CardTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests\Unit\DeckTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit  --testdox tests/Unit/DealerTest.php --exclude skip

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox tests/Unit/DealerTest.php --filter it_can_deal_cards_to_multiple_players_at_a_table

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox tests/Unit/GamePlayTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox tests/Unit/HandIdentifierTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox tests/Unit/HandTypeTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests/Unit/TableSeatTest.php

C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests/Unit/PotTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests/Unit/PotHelperTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests/Unit/BetHelperTest.php

php application.php app:build-env
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox  tests/Unit/GamePlayPotTest.php

