<?php

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';
require_once __DIR__ . '/../app/controllers/SchedulingController.php';
require_once __DIR__ . '/../app/APIs/BarberApi.php';

$action = $_GET['action'] ?? 'login';

$authController = new AuthController($pdo);
$userController = new UserController($pdo);
$schedulingController = new SchedulingController($pdo);
$reviewController = new SchedulingReviewController($pdo);

switch ($action) {

    case 'login':
        $authController->login();
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

    case 'forgot_password':
        $authController->forgotPassword();
        break;

    case 'forgot_password_submit':
        $authController->handleForgotPassword();
        break;

    case 'reset_password_form':
        $authController->resetPasswordForm();
        break;

    case 'reset_password':
        $authController->resetPassword();
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

    case 'review_pending':
        $reviewController->pending();
        break;

    case 'review_accept':
        $reviewController->accept();
        break;

    case 'review_reject':
        $reviewController->reject();
        break;

    case 'api_barber_index':
        $api = new BarberApi();
        $api->index();
        break;

    case 'api_barber_show':
        $api = new BarberApi();
        $api->show();
        break;

    case 'api_barber_services':
        $api = new BarberApi();
        $api->getServicesByBarber();
        break;

    case 'api_barber_store':
        $api = new BarberApi();
        $api->store();
        break;

    case 'api_barber_update':
        $api = new BarberApi();
        $api->update();
        break;

    case 'admin_barbers':
        require_once __DIR__ . '/../app/views/admin/barbers.php';
        break;
        
    case 'api_barber_update_services':
        $api = new BarberApi();
        $api->updateServices();
        break;

    case 'api_barber_delete':
        $api = new BarberApi();
        $api->delete();
        break;

    default:
        echo "Rota não encontrada.";
        break;
}
