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
        $agendamentos = $this->reviewModel->allPending();
        $message = $_GET['message'] ?? null;

        require_once __DIR__ . '/../views/admin/acceptOrRefuse.php';
    }

    public function accept()
    {
        $idAgendamento = $_POST['id_agendamento'] ?? null;

        if (empty($idAgendamento)) {
            echo "Agendamento não informado.";
            return;
        }

        try {
            $this->reviewModel->accept((int)$idAgendamento);

            header('Location: index.php?action=review_pending&message=accepted');
            exit;

        } catch (Exception $e) {
            echo "Erro ao aceitar agendamento: " . $e->getMessage();
        }
    }

    public function reject()
    {
        $idAgendamento = $_POST['id_agendamento'] ?? null;

        if (empty($idAgendamento)) {
            echo "Agendamento não informado.";
            return;
        }

        try {
            $this->reviewModel->reject((int)$idAgendamento);

            header('Location: index.php?action=review_pending&message=rejected');
            exit;

        } catch (Exception $e) {
            echo "Erro ao recusar agendamento: " . $e->getMessage();
        }
    }
}