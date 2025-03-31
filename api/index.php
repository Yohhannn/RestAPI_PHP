<?php
require_once 'config/Database.php';
require_once 'core/Router.php';

$router = new Router();
$router->handleRequest();