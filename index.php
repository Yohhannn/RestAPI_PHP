<?php

use config\Router;

require_once 'config/Database.php';
require_once 'config/Router.php';

$router = new Router();
$router->handleRequest();