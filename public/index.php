<?php

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';
require_once __DIR__ . '/../app/controllers/SchedulingController.php';
require_once __DIR__ . '/../app/APIs/SchedulingApi.php';
require_once __DIR__ . '/../app/APIs/AuthApi.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        (new AuthController($pdo))->login();
        break;

    case 'logout':
       (new AuthController($pdo))->logout();
        break;

    case 'home':
        (new AuthController($pdo))->home();
        break;

    case 'forgot_password':
        (new AuthController($pdo))->forgotPassword();
        break;

    case 'forgot_password_submit':
        (new AuthController($pdo))->handleForgotPassword();
        break;

    case 'reset_password_form':
        (new AuthController($pdo))->resetPasswordForm();
        break;

    case 'reset_password':
        (new AuthController($pdo))->resetPassword();
        break;

    case 'user_create':
        (new UserController($pdo))->create();
        break;

    case 'user_store':
        (new UserController($pdo))->store();
        break;

    case 'scheduling_create':
        (new SchedulingController($pdo))->create();
        break;

    case 'scheduling_store':
        (new SchedulingController($pdo))->store();
        break;

    case 'scheduling_get':
        (new SchedulingController($pdo))->get();
        break;

    case 'scheduling_edit':
        (new SchedulingController($pdo))->edit();
        break;

    case 'scheduling_update':
        (new SchedulingController($pdo))->update();
        break;

    case 'review_pending':
        (new SchedulingReviewController($pdo))->pending();
        break;

    case 'review_accept':
        (new SchedulingReviewController($pdo))->accept();
        break;

    case 'review_reject':
        (new SchedulingReviewController($pdo))->reject();
        break;

    case 'api_auth_login':
        (new AuthApi($pdo))->login();
        break;

    case 'api_auth_me':
        (new AuthApi($pdo))->me();
        break;

    case 'api_auth_logout':
        (new AuthApi($pdo))->logout();
        break;

    case 'api_schedulings_list':
        (new SchedulingApi($pdo))->index();
        break;

    case 'api_schedulings_get':
        (new SchedulingApi($pdo))->show();
        break;

    case 'api_scheduling_store':
        (new SchedulingApi($pdo))->store();
        break;
    
    case 'api_scheduling_get_all':
        (new SchedulingApi($pdo))->getAll();
        break;

    case 'api_scheduling_get_by_id':
        (new SchedulingApi($pdo))->getById($_GET['id'] ?? null);
        break;

    case 'api_scheduling_update':
        (new SchedulingApi($pdo)) ->update($_GET['id'] ?? null);
        break;
    
    case 'api_scheduling_delete':
        (new SchedulingApi($pdo))->delete($_GET['id'] ?? null);
        break;


    case 'api_scheduling_my_active':
        (new SchedulingApi($pdo))->getMyActive();
        break;
    
    case 'scheduling_list':
        (new SchedulingController($pdo))->myAppointments();
        break;

    case 'api_scheduling_available_times':
        (new SchedulingApi($pdo))->getAvailableTimes();
        break;

    case 'api_scheduling_demo_get_by_id':
        (new SchedulingApi($pdo))->demoGetById($_GET['id'] ?? null);
        break;

    case 'api_scheduling_demo_delete':
        (new SchedulingApi($pdo))->demoDeleteById($_GET['id'] ?? null);
        break;
    
    
    default:
        echo 'Rota não encontrada.';
        break;
}