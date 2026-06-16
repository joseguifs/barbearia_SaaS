<?php

require_once __DIR__ . '/../models/AdminScheduling.php';
require_once __DIR__ . '/../models/AdminClient.php';

class AdminPanelController
{
    private $pdo;
    private $schedulingModel;
    private $clientModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->schedulingModel = new AdminScheduling($pdo);
        $this->clientModel = new AdminClient($pdo);
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

    private function normalizeDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        return str_replace('T', ' ', $value) . ':00';
    }

    private function redirect($route, $message = null)
    {
        $url = "index.php?action={$route}";

        if ($message) {
            $url .= "&message={$message}";
        }

        header("Location: {$url}");
        exit;
    }

    public function schedulings()
    {
        $this->requireAdmin();

        $filters = [
            'status' => $_GET['status'] ?? '',
            'data' => $_GET['data'] ?? '',
            'busca' => trim($_GET['busca'] ?? '')
        ];

        $agendamentos = $this->schedulingModel->getAll($filters);

        $clientes = $this->schedulingModel->getClients();
        $barbeiros = $this->schedulingModel->getBarbers();
        $servicos = $this->schedulingModel->getServices();

        require_once __DIR__ . '/../views/admin/schedulings.php';
    }

    public function storeScheduling()
    {
        $this->requireAdmin();

        $data = [
            'id_cliente' => (int) ($_POST['id_cliente'] ?? 0),
            'id_barbeiro' => (int) ($_POST['id_barbeiro'] ?? 0),
            'data_hora' => $this->normalizeDateTime($_POST['data_hora'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => $_POST['status'] ?? 'pendente',
            'servicos' => $_POST['servicos'] ?? []
        ];

        if (!$data['id_cliente'] || !$data['id_barbeiro'] || !$data['data_hora']) {
            $this->redirect('admin_schedulings', 'invalid');
        }

        $this->schedulingModel->create($data);

        $this->redirect('admin_schedulings', 'created');
    }

    public function updateScheduling()
    {
        $this->requireAdmin();

        $idAgendamento = (int) ($_POST['id_agendamento'] ?? 0);

        if ($idAgendamento <= 0) {
            $this->redirect('admin_schedulings', 'invalid');
        }

        $data = [
            'id_cliente' => (int) ($_POST['id_cliente'] ?? 0),
            'id_barbeiro' => (int) ($_POST['id_barbeiro'] ?? 0),
            'data_hora' => $this->normalizeDateTime($_POST['data_hora'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => $_POST['status'] ?? 'pendente',
            'servicos' => $_POST['servicos'] ?? []
        ];

        if (!$data['id_cliente'] || !$data['id_barbeiro'] || !$data['data_hora']) {
            $this->redirect('admin_schedulings', 'invalid');
        }

        $this->schedulingModel->update($idAgendamento, $data);

        $this->redirect('admin_schedulings', 'updated');
    }

    public function deleteScheduling()
    {
        $this->requireAdmin();

        $idAgendamento = (int) ($_POST['id_agendamento'] ?? 0);

        if ($idAgendamento <= 0) {
            $this->redirect('admin_schedulings', 'invalid');
        }

        $this->schedulingModel->delete($idAgendamento);

        $this->redirect('admin_schedulings', 'deleted');
    }

    public function clients()
    {
        $this->requireAdmin();

        $busca = trim($_GET['busca'] ?? '');
        $clientes = $this->clientModel->getAll($busca);

        require_once __DIR__ . '/../views/admin/clients.php';
    }

    public function storeClient()
    {
        $this->requireAdmin();

        $nome = trim($_POST['nome'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if ($nome === '' || $email === '' || $senha === '') {
            $this->redirect('admin_clients', 'invalid');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $this->clientModel->create([
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email,
            'senha' => $senhaHash
        ]);

        $this->redirect('admin_clients', 'created');
    }

    public function updateClient()
    {
        $this->requireAdmin();

        $idCliente = (int) ($_POST['id_cliente'] ?? 0);

        if ($idCliente <= 0) {
            $this->redirect('admin_clients', 'invalid');
        }

        $nome = trim($_POST['nome'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if ($nome === '' || $email === '') {
            $this->redirect('admin_clients', 'invalid');
        }

        $senhaHash = $senha !== '' ? password_hash($senha, PASSWORD_DEFAULT) : null;

        $this->clientModel->update($idCliente, [
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email,
            'senha' => $senhaHash
        ]);

        $this->redirect('admin_clients', 'updated');
    }

    public function deleteClient()
    {
        $this->requireAdmin();

        $idCliente = (int) ($_POST['id_cliente'] ?? 0);

        if ($idCliente <= 0) {
            $this->redirect('admin_clients', 'invalid');
        }

        try {
            $this->clientModel->delete($idCliente);
            $this->redirect('admin_clients', 'deleted');

        } catch (PDOException $e) {
            $this->redirect('admin_clients', 'delete_error');
        }
    }
}