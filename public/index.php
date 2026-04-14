no seu index tenta isso:

<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/SchedulingReviewController.php';

$action = $_GET['action'] ?? 'home';

$reviewController = new SchedulingReviewController($pdo);


switch ($action) {
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
        echo "Ação inválida.";
        break;
}