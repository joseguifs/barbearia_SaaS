<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/SchedulingController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';

$action = $_GET['action'] ?? 'home';

$schedulingController = new SchedulingController($pdo);
$userController = new UserController($pdo);
$reviewController = new SchedulingReviewController($pdo);

switch ($action) {
    case 'scheduling_create':
        $schedulingController->create();
        break;

    case 'scheduling_store':
        $schedulingController->store();
        break;

    case 'scheduling_get':
        $schedulingController->get();
        break;

    case 'user_create':
        $userController->create();
        break;

    case 'user_store':
        $userController->store();
        break;

    case 'review_pending':
        $reviewController->pending();
        break;

    case 'review_accept':
        $reviewController->accept();
        break;

    case 'review_reject':
        $reviewController->reject();
        break;	

    default:
        echo "Rota não encontrada.";
        break;
}
