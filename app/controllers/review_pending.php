<?php

require_once __DIR__ . '/../models/Scheduling.php';

class AdminController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function pending()
    {
        $model = new Scheduling($this->pdo);
        $agendamentos = $model->getPending();

        if (!$agendamentos) {
            $agendamentos = [];
        }

        require __DIR__ . '/../views/admin/pending.php';
    }

    public function accept()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /index.php?action=review_pending");
            exit;
        }

        $id = $_POST['id_agendamento'] ?? null;

        if (!$id) {
            die("ID inválido");
        }

        $model = new Scheduling($this->pdo);

        if (!$model->isPending($id)) {
            die("Agendamento não está pendente");
        }

        $model->updateStatus($id, 'agendado');

        header("Location: /index.php?action=review_pending&success=Agendamento aceito");
        exit;
    }

    public function reject()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /index.php?action=review_pending");
            exit;
        }

        $id = $_POST['id_agendamento'] ?? null;

        if (!$id) {
            die("ID inválido");
        }

        $model = new Scheduling($this->pdo);

        if (!$model->isPending($id)) {
            die("Agendamento não está pendente");
        }

        try {
            $this->pdo->beginTransaction();

            $model->deleteServices($id);
            $model->updateStatus($id, 'cancelado');

            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Erro ao cancelar agendamento");
        }

        header("Location: /index.php?action=review_pending&success=Agendamento recusado");
        exit;
    }
}