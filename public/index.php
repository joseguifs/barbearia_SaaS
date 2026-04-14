<?php

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/controllers/AdminController.php';

$action = $_GET['action'] ?? '';

$controller = new AdminController($pdo);

switch ($action) {

    case 'review_pending':
        $controller->pending();
        break;

    case 'review_accept':
        $controller->accept();
        break;

    case 'review_reject':
        $controller->reject();
        break;

    default:
        echo "Rota inválida";
        break;
}