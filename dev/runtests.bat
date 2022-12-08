php .\dev\PokerGameApp.php app:create-database -d true
php .\dev\PokerGameApp.php app:build-card-games -d true
php .\dev\PokerGameApp.php app:build-poker-game -d true
C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php ./vendor/bin/phpunit --testdox
