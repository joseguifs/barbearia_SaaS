<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/ClientController.php';

$controller = new ClientController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

$controller->handleAPI($method, $id);