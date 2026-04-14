<?php

// Conexão com banco
require_once __DIR__ . '/../config/database.php';

// Controllers
require_once __DIR__ . '/../app/controllers/AdminController.php';

// Captura da ação
$action = $_GET['action'] ?? '';

// Instancia controller
$controller = new AdminController($pdo);

// Rotas
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