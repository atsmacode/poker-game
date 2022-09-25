<!DOCTYPE html>
<html>
<header>
    <link href="https://fonts.googleapis.com/css2?family=Zilla+Slab:ital,wght@0,300;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">

    <title>Read Right Hands - Vanilla</title>
</header>
<body class="bg-dark text-white">

<div id="app" class="container-sm">

    <?php require('nav.php'); ?>

    <h1>Welcome</h1>

    <p>Read Right Hands is a simple poker game developed in Laravel.</p>

    <div class="ms-1 mb-3">
        <a class="btn btn-primary" href="play">Play Now!</a>
    </div>

    <div class="bg-primary p-3 rounded mb-1">

        <div class="row">

            <p class="m-0">Game and hand info will be displayed here.</p>

        </div>

    </div>

    <div class="bg-secondary p-3 rounded mb-1">

        <div class="row">

            <h1>Players</h1>

            <p>Players will be displayed here.</p>

        </div>

    </div>

    <div class="bg-success p-3 rounded mb-1">
        <div class="row">
            <div class="col">
                <h2>Community Cards</h2>
                <p>Community cards will be dealt here.</p>
            </div>
        </div>
    </div>


    <div>
        <div class="bg-info p-3 rounded mb-1">
            <h2>Winner</h2>
            <p>The winner of the hand will be shown here.</p>

        </div>
    </div>

</div>
</body>
<script src="/js/app.js"></script>
<link rel="stylesheet" href="/css/app.css"> 
</html>
