require_once __DIR__ . '/../app/controllers/AdminController.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'review_pending':
        $controller = new AdminController($pdo);
        $controller->pending();
        break;

    case 'review_accept':
        $controller = new AdminController($pdo);
        $controller->accept();
        break;

    case 'review_reject':
        $controller = new AdminController($pdo);
        $controller->reject();
        break;

    default:
        echo "Rota inválida";
        break;
}