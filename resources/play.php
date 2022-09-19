<!DOCTYPE html>
<html>
<header>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Zilla+Slab:ital,wght@0,300;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">

    <title>Read Right Hands - Vanilla</title>
</header>
<body class="bg-dark text-white">

    <div id="app" class="container-sm">

        <?php require('nav.php'); ?>

        <div class="bg-primary p-3 rounded mb-1">

            <div class="row">

                <p class="m-0"><strong>Hand Info</strong> Pot: </p>

            </div>

        </div>

        <div class="bg-secondary p-3 rounded mb-1">

            <div class="row">

                <h1>Players</h1>

            </div>

        </div>

        <div class="bg-success p-3 rounded mb-1">
            <div class="row">
                <div class="col">
                    <h2>Community Cards</h2>
                </div>
            </div>
        </div>


        <div>
            <div class="bg-info p-3 rounded mb-1">
                <h2>Winner</h2>
            </div>
        </div>

    </div>
</body>
</html>