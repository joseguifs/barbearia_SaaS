<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SchedulingController.php';


$action = $_GET['action'] ?? 'login';

$authController = new AuthController($pdo);
$userController = new UserController($pdo);
$schedulingController = new SchedulingController($pdo);

switch ($action) {
    case 'login':
        $authController->login();
        break;

    case 'home':
        $authController->home();
        break;
    
    case 'forgot_password':
        $authController->forgotPassword();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'user_create':
        $userController->create();
        break;

    case 'user_store':
        $userController->store();
        break;

    case 'scheduling_create':
        $schedulingController->create();
        break;

    case 'authenticate':
        $authController->authenticate();
        break;

    case 'home':
        $authController->home();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'scheduling_edit':
        $schedulingController->edit();
        break;

    case 'scheduling_update':
        $schedulingController->update();
        break;

    case 'user_create':
        $userController->create();
        break;

    case 'user_store':
        $userController->store();
        break;

    case 'scheduling_create':
        $schedulingController->create();
        break;

    case 'scheduling_store':
        $schedulingController->store();
        break;

    case 'scheduling_get':
        $schedulingController->get();
        break;

    case 'scheduling_edit':
        $schedulingController->edit();
        break;

    case 'scheduling_update':
        $schedulingController->update();
        break;
    case 'logout':
        $authController->logout();
        break;
    default:
        echo "Rota não encontrada.";
        break;
}