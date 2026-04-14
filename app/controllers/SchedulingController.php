<?php

require_once __DIR__ . '/../models/Barber.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Scheduling.php';
require_once __DIR__ . '/../models/Client.php';

class SchedulingController
{
    private $barberModel;
    private $serviceModel;
    private $schedulingModel;
    private $clientModel;

    public function __construct($pdo)
    {
        $this->barberModel = new Barber($pdo);
        $this->serviceModel = new Service($pdo);
        $this->schedulingModel = new Scheduling($pdo);
        $this->clientModel = new Client($pdo);
    }

    private function renderCreate(array $formData = [], ?string $errorMessage = null, bool $success = false)
    {
        $clientes = $this->clientModel->all();
        $barbeiros = $this->barberModel->all();
        $servicos = $this->serviceModel->all();

        require __DIR__ . '/../views/scheduling/create.php';
    }

    public function create()
    {
        $success = isset($_GET['success']);
        $this->renderCreate([], null, $success);
    }

    public function store()
    {
        $formData = [
            'cliente_id' => $_POST['cliente_id'] ?? '',
            'barbeiro_id' => $_POST['barbeiro_id'] ?? '',
            'servicos' => $_POST['servicos'] ?? [],
            'data_agendamento' => $_POST['data_agendamento'] ?? '',
            'hora_agendamento' => $_POST['hora_agendamento'] ?? '',
            'descricao' => trim($_POST['descricao'] ?? '')
        ];

        $idCliente = $formData['cliente_id'];
        $idBarbeiro = $formData['barbeiro_id'];
        $servicos = $formData['servicos'];
        $data = $formData['data_agendamento'];
        $hora = $formData['hora_agendamento'];
        $descricao = $formData['descricao'];

        if (empty($idCliente) || empty($idBarbeiro) || empty($servicos) || empty($data) || empty($hora)) {
            $this->renderCreate($formData, 'Preencha cliente, barbeiro, serviço(s), data e horário.');
            return;
        }

        $idCliente = (int) $idCliente;
        $idBarbeiro = (int) $idBarbeiro;
        $servicos = array_map('intval', $servicos);
        $formData['servicos'] = $servicos;

        if (!$this->clientModel->find($idCliente)) {
            $this->renderCreate($formData, 'Cliente inválido.');
            return;
        }

        if (!$this->serviceModel->barberHasAllServices($idBarbeiro, $servicos)) {
            $this->renderCreate($formData, 'O barbeiro selecionado não atende todos os serviços escolhidos.');
            return;
        }

        $dataHora = $data . ' ' . $hora . ':00';

        $dados = [
            'id_cliente' => $idCliente,
            'id_barbeiro' => $idBarbeiro,
            'data_hora' => $dataHora,
            'descricao' => $descricao,
            'status' => 'pendente'
        ];

        try {
            $this->schedulingModel->create($dados, $servicos);
            header('Location: index.php?action=scheduling_create&success=1');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->renderCreate($formData, 'Esse barbeiro já possui agendamento nesse horário.');
                return;
            }

            $this->renderCreate($formData, 'Erro ao salvar agendamento: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->renderCreate($formData, 'Erro ao salvar agendamento: ' . $e->getMessage());
        }
    }

    public function get()
    {
        $idAgendamento = $_GET['id'] ?? null;

        if (!$idAgendamento) {
            echo "Agendamento não informado.";
            return;
        }

        $agendamento = $this->schedulingModel->find($idAgendamento);

        if (!$agendamento) {
            echo "Agendamento não encontrado.";
            return;
        }

        $servicos = $this->schedulingModel->getServicesBySchedulingId($idAgendamento);
        $valorTotal = $this->schedulingModel->getTotalValueBySchedulingId($idAgendamento);

        require_once __DIR__ . '/../views/scheduling/get.php';
    }
}