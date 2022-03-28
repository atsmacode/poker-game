<!DOCTYPE html>
<html>
<header>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Zilla+Slab:ital,wght@0,300;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">

    <title>Read Right Hands - Vanilla</title>
</header>
<body class="bg-dark text-white">

    <div id="app" class="container-sm">

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid ps-0">

                <a class="navbar-brand" href="ViewController.php?requested_page=index"><strong><span class="text-danger">Read</span></strong> Right Hands</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="ViewController.php?requested_page=play">Play</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Hand History</a>
                        </li>
                    </ul>

                </div>

            </div>
        </nav>

        <div class="bg-primary p-3 rounded mb-1">

            <div class="row">

                <p class="m-0"><strong>Hand Info</strong> Pot: </p>

            </div>

        </div>

        <div class="bg-secondary p-3 rounded mb-1">

            <div class="row">

                <h1>Players</h1>

                <!--<div class="col-4 mb-3">

                    <player :player="players[0]" :winner="winner"></player>


                </div>

                <div class="col-4 mb-3">

                    <player :player="players[1]" :winner="winner"></player>

                </div>

                <div class="col-4 mb-3">

                    <player :player="players[2]" :winner="winner"></player>


                </div>-->

            </div>

            <!--<div class="row">

                <div class="col-4 mb-3">

                    <player :player="players[5]" :winner="winner"></player>

                </div>

                <div class="col-4 mb-3">

                    <player :player="players[4]" :winner="winner"></player>

                </div>

                <div class="col-4 mb-3">

                    <player :player="players[3]" :winner="winner"></player>

                </div>

            </div>-->

        </div>

        <div class="bg-success p-3 rounded mb-1">
            <div class="row">
                <div class="col">
                    <h2>Community Cards</h2>
                    <!--<div v-if="communityCards.length > 0">
                        <div class="row mb-2 ms-0">
                            <div v-for="card in communityCards" class="m-0 bg-white ms-1" v-bind:class="suitColours[card.suit]" style="width:100px;height:130px">
                                <div class="card-body ps-1 pe-0">
                                    <p class="fs-2"><strong>@{{card.rank}}</strong> @{{card.suitAbbreviation}}</p>
                                </div>
                            </div>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>


        <div>
            <div class="bg-info p-3 rounded mb-1">
                <h2>Winner</h2>
                <!--<p>Player @{{winner.player.id}} with @{{winner.handType.name}}</p>
                <button v-on:click="gameData" class="btn btn-primary">
                    Next Hand
                </button>-->
            </div>
        </div>

    </div>
</body>
</html>