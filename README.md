# Commands

Run the unit test suite on Linux:

>./runtests suite

Run the unit test suite on Windows:

>./runtests.bat

Drop, Create and Seed all tables. '-d true' is required to run this in test DB

> php application.php app:build-env -d true

# Todo:

- Review how theplayer after Dealer is retrieved, I suspect it is a bit off
- Convert GamePlay class into a middleware pipeline
    - Class can be divided into different classes responsible for a different set of actions like:
        - Who goes next
        - What hand stage is next /What stage the hand is in
        - What are the available options for the next player
- Add custom join queries for relationships
    - Rather than multiple chained model calls resulting in a lot of queries
- Address updated_at reliance
    - Currenly manually setting these values so the expected 'action on' seat can be identified in tests
    - Could use UNIX timestamp in miliseconds value
- Use constants for cards, suits, rankings & deck instead of DB
- Review all TODO comments and implement solution
- Once everything above is tidied/finalised, add remaining unit tests from original app
- Also look into proper DB refresh solution as my bash script one does fail sometimes (Symfony component? I want to avoid requiring an entire framework)
