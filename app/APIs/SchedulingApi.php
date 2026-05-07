<?php

require_once __DIR__ . '/../models/Scheduling.php';

class SchedulingApi

{
    private $schedulingModel;

    public function __construct($pdo)
    {
        $this->schedulingModel = new Scheduling($pdo);
    }

    private function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function json($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function requireAuth()
    {
        if (empty($_SESSION['id_cliente'])) {
            $this->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }
    }

    public function index()
    {
        // TEMPORÁRIO: sem autenticação
        $agendamentos = $this->schedulingModel->all();

        $this->json([
            'success' => true,
            'data' => $agendamentos
        ]);
    }

    public function show()
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'ID inválido.'
            ], 400);
        }

        $agendamento = $this->schedulingModel->find($id);

        if (!$agendamento) {
            $this->json([
                'success' => false,
                'message' => 'Agendamento não encontrado.'
            ], 404);
        }

        $servicos = $this->schedulingModel->getServicesBySchedulingId($id);
        $valorTotal = $this->schedulingModel->getTotalValueBySchedulingId($id);

        $agendamento['servicos'] = $servicos;
        $agendamento['valor_total'] = $valorTotal;

        $this->json([
            'success' => true,
            'data' => $agendamento
        ]);
    }

    public function store()
    {
        $this->requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            $this->json([
                'success' => false,
                'message' => 'JSON inválido.'
            ], 400);
        }

        $idCliente = $_SESSION['id_cliente'];
        $idBarbeiro = (int)($input['barbeiro_id'] ?? 0);
        $servicos = $input['servicos'] ?? [];
        $data = trim($input['data_agendamento'] ?? '');
        $hora = trim($input['hora_agendamento'] ?? '');
        $descricao = trim($input['descricao'] ?? '');

        if ($idBarbeiro <= 0 || empty($servicos) || $data === '' || $hora === '') {
            $this->json([
                'success' => false,
                'message' => 'Preencha os campos obrigatórios.'
            ], 422);
        }

        $dataHora = $data . ' ' . $hora . ':00';

        $novoId = $this->schedulingModel->createWithServices(
            $idCliente,
            $idBarbeiro,
            $dataHora,
            $descricao,
            $servicos
        );

        if (!$novoId) {
            $this->json([
                'success' => false,
                'message' => 'Não foi possível criar o agendamento.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Agendamento criado com sucesso.',
            'id' => $novoId
        ], 201);
    }

    public function delete()
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'ID inválido.'
            ], 400);
        }

        $ok = $this->schedulingModel->deleteByIdAndClient($id, $_SESSION['id_cliente']);

        if (!$ok) {
            $this->json([
                'success' => false,
                'message' => 'Não foi possível excluir o agendamento.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Agendamento excluído com sucesso.'
        ]);
    }
}