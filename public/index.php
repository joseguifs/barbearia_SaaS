<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/ServiceController.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';
require_once __DIR__ . '/../app/controllers/SchedulingController.php';
require_once __DIR__ . '/../app/controllers/BarberController.php';

// Junto com as outras instâncias, adicione:
$barberController = new BarberController($pdo);

// Junto com as outras instâncias, adicione:
$barberController = new BarberController($pdo);



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

    case 'api_scheduling_get_all': # api de agendamento começa aqui
        (new SchedulingApi($pdo))->getAll();
        break;

    case 'api_scheduling_demo_get_by_id': 
        (new SchedulingApi($pdo))->demoGetById($_GET['id'] ?? null);
        break;

    case 'api_scheduling_demo_get_by_id':
            (new SchedulingApi($pdo))->demoPost();
            break;

    case 'api_scheduling_demo_update':
        (new SchedulingApi($pdo))->demoUpdate($_GET['id'] ?? null);
        break;

    case 'api_scheduling_demo_delete': # termina aqui
        (new SchedulingApi($pdo))->demoDeleteById($_GET['id'] ?? null);
        break;

    case 'api_auth_reset_password':
        (new AuthApi($pdo))->resetPassword();
        break;

    case 'api_user': # api de usuarios, com todos os verbos para teste
        (new ClientApiController($pdo))->handleRequest();
        break;

    case 'api_barber_index': # comeca aqui
        (new BarberApi($pdo))->index();
        break;
        
    case 'barber_profile':
        $barberController->profile();
        break;

    case 'api_barber_show':
        (new BarberApi($pdo))->show();
        break;

    case 'api_barber_services':
        (new BarberApi($pdo))->getServicesByBarber();
        break;

    case 'api_barber_store':
        (new BarberApi($pdo))->store();
        break;

    case 'api_barber_update':
        (new BarberApi($pdo))->update();
        break;

    case 'admin_barbers':
        require_once __DIR__ . '/../app/views/admin/barbers.php';
        break;
        
    case 'api_barber_update_services':
        (new BarberApi($pdo))->updateServices();
        break;

    case 'api_barber_delete': # esse seria o ultimo
        (new BarberApi($pdo))->delete();
        break;
    
    case 'services':
        (new ServiceController($pdo))->index();
        break;

     case 'api_service_index': # comeca aqui
        (new ServiceController($pdo))->apiIndex();
        break;
    
    case 'api_service_get':
        (new ServiceController($pdo))->apiShow();
        break;

    case 'api_service_store':
        (new ServiceController($pdo))->apiStore();
        break;

    case 'api_service_update':
        (new ServiceController($pdo))->apiUpdate();
        break;
    
    case 'api_service_delete': # termina aqui
        (new ServiceController($pdo))->apiDelete();
        break;
    
    case 'profile':
        (new ProfileController($pdo))->show();
        break;

    case 'profile_edit':
        (new ProfileController($pdo))->edit();
        break;

    case 'profile_update':
        (new ProfileController($pdo))->update();
        break;
    default:
        echo 'Rota não encontrada.';
        break;
}