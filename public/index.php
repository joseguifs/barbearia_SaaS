<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/UserController.php';


$action = $_GET['action'] ?? 'user_create';

$userController = new UserController($pdo);


switch ($action) {
    case 'user_create':
        $userController->create();
        break;

    case 'user_store':
        $userController->store();
        break;

    default:
        echo "Rota não encontrada.";
        break;
}