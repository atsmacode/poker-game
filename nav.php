<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid ps-0">

        <a class="navbar-brand" href="/ViewController.php?requested_page=index"><strong><span class="text-danger">Read</span></strong> Right Hands - <span class="text-warning">Vanilla</span></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/play.php') echo 'active'; ?>" aria-current="page" href="/play.php">Play</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Hand History</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/database/seeders/index.php') echo 'active'; ?>" href="/database/seeders/index.php">Build</a>
                </li>
            </ul>

        </div>

    </div>
</nav>
