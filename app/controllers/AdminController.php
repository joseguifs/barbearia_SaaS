<?php

class AdminController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function requireAdmin()
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['id_admin'])) {
            header('Location: index.php?action=admin_login');
            exit;
        }
    }

    public function home()
    {
        $this->requireAdmin();

        $stats = $this->getDashboardStats();

        require_once __DIR__ . '/../views/admin/home.php';
    }

    private function getDashboardStats()
    {
        return [
            'clientes' => $this->countTable('cliente'),
            'barbeiros' => $this->countTable('barbeiro'),
            'servicos' => $this->countTable('servico'),
            'agendamentos' => $this->countTable('agendamento'),
            'pendentes' => $this->countByStatus('pendente'),
            'agendados' => $this->countByStatus('agendado'),
            'cancelados' => $this->countByStatus('cancelado'),
            'hoje' => $this->countTodaySchedulings()
        ];
    }

    private function countTable($table)
    {
        $allowedTables = ['cliente', 'barbeiro', 'servico', 'agendamento'];

        if (!in_array($table, $allowedTables, true)) {
            return 0;
        }

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$table}");
        return (int) $stmt->fetchColumn();
    }

    private function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) FROM agendamento WHERE status = :status";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function countTodaySchedulings()
    {
        $sql = "SELECT COUNT(*) FROM agendamento WHERE DATE(data_hora) = CURDATE()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}