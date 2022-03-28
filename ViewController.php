<?php

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    require($_REQUEST['requested_page'] . '.php');
}

