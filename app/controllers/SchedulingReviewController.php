<?php

require_once __DIR__ . '/../models/SchedulingReview.php';

class SchedulingReviewController
{
    private $reviewModel;

    public function __construct($pdo)
    {
        $this->reviewModel = new SchedulingReview($pdo);
    }

    public function pending()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_barbeiro'])) {
            header('Location: index.php?action=barber_login');
            exit;
        }

        $idBarbeiro = (int) $_SESSION['id_barbeiro'];

        $agendamentos = $this->reviewModel->getPendingByBarber($idBarbeiro);

        require_once __DIR__ . '/../views/admin/acceptOrRefuse.php';
    }

    public function accept()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_barbeiro'])) {
            header('Location: index.php?action=barber_login');
            exit;
        }

        $idAgendamento = (int) ($_POST['id_agendamento'] ?? 0);
        $idBarbeiro = (int) $_SESSION['id_barbeiro'];

        if ($idAgendamento <= 0) {
            header('Location: index.php?action=review_pending&message=invalid');
            exit;
        }

        $success = $this->reviewModel->acceptByBarber($idAgendamento, $idBarbeiro);

        if (!$success) {
            header('Location: index.php?action=review_pending&message=error');
            exit;
        }

        header('Location: index.php?action=review_pending&message=accepted');
        exit;
    }

    public function reject()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_barbeiro'])) {
            header('Location: index.php?action=barber_login');
            exit;
        }

        $idAgendamento = (int) ($_POST['id_agendamento'] ?? 0);
        $idBarbeiro = (int) $_SESSION['id_barbeiro'];

        if ($idAgendamento <= 0) {
            header('Location: index.php?action=review_pending&message=invalid');
            exit;
        }

        $success = $this->reviewModel->rejectByBarber($idAgendamento, $idBarbeiro);

        if (!$success) {
            header('Location: index.php?action=review_pending&message=error');
            exit;
        }

        header('Location: index.php?action=review_pending&message=rejected');
        exit;
    }

    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_barbeiro'])) {
            header('Location: index.php?action=barber_login');
            exit;
        }

        $idAgendamento = (int) ($_GET['id'] ?? 0);
        $idBarbeiro = (int) $_SESSION['id_barbeiro'];

        if ($idAgendamento <= 0) {
            header('Location: index.php?action=review_pending&message=invalid');
            exit;
        }

        $agendamento = $this->reviewModel->findPendingByIdAndBarber($idAgendamento, $idBarbeiro);

        if (!$agendamento) {
            header('Location: index.php?action=review_pending&message=not_found');
            exit;
        }

        require_once __DIR__ . '/../views/admin/schedulingDetails.php';
    }
}