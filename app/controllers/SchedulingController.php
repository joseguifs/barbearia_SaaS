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

    public function create()
    {
        $clientes = $this->clientModel->all();
        $barbeiros = $this->barberModel->all();
        $servicos = $this->serviceModel->all();
        $success = isset($_GET['success']) ? true : false;

        require_once __DIR__ . '/../views/scheduling/create.php';
    }

    public function store()
    {
        $idCliente = $_POST['cliente_id'] ?? null;
        $idBarbeiro = $_POST['barbeiro_id'] ?? null;
        $servicos = $_POST['servicos'] ?? [];
        $data = $_POST['data_agendamento'] ?? null;
        $hora = $_POST['hora_agendamento'] ?? null;
        $descricao = trim($_POST['descricao'] ?? '');

        if (empty($idCliente) || empty($idBarbeiro) || empty($servicos) || empty($data) || empty($hora)) {
            echo "Preencha cliente, barbeiro, serviço(s), data e horário.";
            return;
        }

        $idCliente = (int) $idCliente;
        $idBarbeiro = (int) $idBarbeiro;
        $servicos = array_map('intval', $servicos);

        if (!$this->clientModel->find($idCliente)) {
            echo "Cliente inválido.";
            return;
        }

        if (!$this->serviceModel->barberHasAllServices($idBarbeiro, $servicos)) {
            echo "O barbeiro selecionado não atende todos os serviços escolhidos.";
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
                echo "Esse barbeiro já possui agendamento nesse horário.";
                return;
            }

            echo "Erro ao salvar agendamento: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erro ao salvar agendamento: " . $e->getMessage();
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

    public function edit()
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

        $nomesServicos = [];
        foreach ($servicos as $servico) {
            $nomesServicos[] = $servico['nome'];
        }

        $agendamento['servicos_texto'] = !empty($nomesServicos)
            ? implode(' + ', $nomesServicos)
            : 'Serviço não informado';

        $agendamento['data_formatada'] = date('d/m/Y', strtotime($agendamento['data_hora']));
        $agendamento['hora_formatada'] = date('H:i', strtotime($agendamento['data_hora']));

        $dataSelecionada = date('Y-m-d', strtotime($agendamento['data_hora']));
        $horaSelecionada = date('H:i', strtotime($agendamento['data_hora']));

        $horariosDisponiveis = [
            '09:00', '10:00', '11:30', '14:00',
            '15:30', '17:00', '18:30', '20:00'
        ];

        require_once __DIR__ . '/../views/scheduling/update.php';
    }

    public function update()
    {
        $idAgendamento = $_POST['id_agendamento'] ?? null;
        $data = $_POST['data_agendamento'] ?? null;
        $hora = $_POST['hora_agendamento'] ?? null;

        if (!$idAgendamento || !$data || !$hora) {
            echo "Dados incompletos para atualização.";
            return;
        }

        $dataHora = $data . ' ' . $hora . ':00';

        $sucesso = $this->schedulingModel->updateDateTime($idAgendamento, $dataHora);

        if ($sucesso) {
            header('Location: index.php?action=home');
            exit;
        }

        echo "Não foi possível atualizar o agendamento.";
    }
}