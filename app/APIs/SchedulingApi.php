<?php

require_once __DIR__ . '/BaseApi.php';
require_once __DIR__ . '/../models/Scheduling.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Client.php';

class SchedulingApi extends BaseApi
{
    private $schedulingModel;
    private $serviceModel;
    private $clientModel;

    public function __construct($pdo)
    {
        $this->schedulingModel = new Scheduling($pdo);
        $this->serviceModel = new Service($pdo);
        $this->clientModel = new Client($pdo);
    }

    public function getAll()
    {
        try {
            $agendamentos = $this->schedulingModel->getAll();

            if (!$agendamentos) {
                $this->json([
                    'success' => true,
                    'message' => 'Nenhum agendamento encontrado.',
                    'data' => []
                ], 200);
            }

            $this->json([
                'success' => true,
                'message' => 'Agendamentos encontrados com sucesso.',
                'data' => $agendamentos
            ], 200);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao buscar os agendamentos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getById($id = null)
    {
        try {
            $this->requireAuth();

            $id = $id ?? ($_GET['id'] ?? null);
            $idCliente = $_SESSION['id_cliente'] ?? null;

            if (!$id) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento não informado.'
                ], 400);
            }

            if (!$idCliente) {
                $this->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $id = (int) $id;
            $idCliente = (int) $idCliente;

            $agendamento = $this->schedulingModel->getByIdAndClient($id, $idCliente);

            if (!$agendamento) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado ou não pertence ao usuário logado.'
                ], 404);
            }

            $this->json([
                'success' => true,
                'message' => 'Agendamento encontrado com sucesso.',
                'data' => $agendamento
            ], 200);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao buscar o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getMyActive()
    {
        try {
            $this->requireAuth();

            $idCliente = $_SESSION['id_cliente'] ?? null;

            if (empty($idCliente)) {
                $this->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $agendamentos = $this->schedulingModel->getActiveByClient((int)$idCliente);

            $this->json([
                'success' => true,
                'message' => 'Agendamentos encontrados com sucesso.',
                'data' => $agendamentos
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao buscar os agendamentos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id = null)
    {
        try {
            $this->requireAuth();

            $id = $id ?? ($_GET['id'] ?? null);
            $idCliente = $_SESSION['id_cliente'] ?? null;

            if (!$id) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento não informado.'
                ], 400);
            }

            if (!$idCliente) {
                $this->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $id = (int) $id;
            $idCliente = (int) $idCliente;

            $agendamento = $this->schedulingModel->getByIdAndClient($id, $idCliente);

            if (!$agendamento) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado ou não pertence ao usuário logado.'
                ], 404);
            }

            if (in_array($agendamento['status'], ['cancelado', 'concluido'], true)) {
                $this->json([
                    'success' => false,
                    'message' => 'Este agendamento não pode mais ser alterado.'
                ], 422);
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input) || empty($input)) {
                $input = $_POST;
            }

            if (!is_array($input) || empty($input)) {
                $this->json([
                    'success' => false,
                    'message' => 'Nenhum dado foi enviado para atualização.'
                ], 400);
            }

            $dados = [];

            /*
                Primeiro tratamos os serviços, porque eles influenciam
                na duração do agendamento e na validação do horário.
            */
            $servicosSelecionados = null;

            if (isset($input['servicos'])) {
                $servicosSelecionados = $input['servicos'];

                if (!is_array($servicosSelecionados)) {
                    $servicosSelecionados = [$servicosSelecionados];
                }

                $servicosSelecionados = array_map('intval', $servicosSelecionados);
                $servicosSelecionados = array_values(array_filter($servicosSelecionados));

                if (empty($servicosSelecionados)) {
                    $this->json([
                        'success' => false,
                        'message' => 'Selecione pelo menos um serviço.'
                    ], 422);
                }

                if (!$this->serviceModel->barberHasAllServices((int)$agendamento['id_barbeiro'], $servicosSelecionados)) {
                    $this->json([
                        'success' => false,
                        'message' => 'O barbeiro selecionado não atende todos os serviços escolhidos.'
                    ], 422);
                }
            }

            /*
                Se o usuário não enviou novos serviços, usamos os serviços atuais
                do agendamento para calcular a duração e validar o horário.
            */
            $servicosParaValidar = $servicosSelecionados;

            if ($servicosParaValidar === null) {
                $servicosAtuais = $this->schedulingModel->getServicesBySchedulingId($id);

                $servicosParaValidar = [];

                foreach ($servicosAtuais as $servicoAtual) {
                    $servicosParaValidar[] = (int) $servicoAtual['id_servico'];
                }
            }

            if (empty($servicosParaValidar)) {
                $this->json([
                    'success' => false,
                    'message' => 'Não foi possível identificar os serviços do agendamento.'
                ], 422);
            }

            /*
                Aceita dois formatos:

                1) data_hora direto:
                {
                    "data_hora": "2026-05-10 14:30:00"
                }

                2) data e hora separados:
                {
                    "data_agendamento": "2026-05-10",
                    "hora_agendamento": "14:30"
                }
            */
            $novaDataHora = null;

            if (!empty($input['data_hora'])) {
                $novaDataHora = trim($input['data_hora']);
            } elseif (!empty($input['data_agendamento']) && !empty($input['hora_agendamento'])) {
                $data = trim($input['data_agendamento']);
                $hora = trim($input['hora_agendamento']);

                $novaDataHora = $data . ' ' . $hora . ':00';
            }

            if ($novaDataHora !== null) {
                $timestamp = strtotime($novaDataHora);

                if (!$timestamp) {
                    $this->json([
                        'success' => false,
                        'message' => 'Data ou horário inválido.'
                    ], 422);
                }

                $novaDataHora = date('Y-m-d H:i:s', $timestamp);

                if (!$this->schedulingModel->isTimeAvailable(
                    (int) $agendamento['id_barbeiro'],
                    $novaDataHora,
                    $servicosParaValidar,
                    $id
                )) {
                    $this->json([
                        'success' => false,
                        'message' => 'Esse horário não está mais disponível para o barbeiro selecionado.'
                    ], 409);
                }

                $dados['data_hora'] = $novaDataHora;
            }

            if (array_key_exists('descricao', $input)) {
                $dados['descricao'] = trim($input['descricao']);
            }

            if (empty($dados) && $servicosSelecionados === null) {
                $this->json([
                    'success' => false,
                    'message' => 'Nenhum dado válido foi enviado para atualização.'
                ], 400);
            }

            /*
                Se o agendamento já estava confirmado/agendado,
                ao solicitar alteração ele volta para pendente.
            */
            if (in_array($agendamento['status'], ['agendado', 'checked'], true)) {
                $dados['status'] = 'pendente';
            }

            $this->schedulingModel->updateScheduleChange(
                $id,
                $dados,
                $servicosSelecionados
            );

            $agendamentoAtualizado = $this->schedulingModel->getByIdAndClient($id, $idCliente);

            $this->json([
                'success' => true,
                'message' => 'Solicitação de alteração enviada com sucesso. O agendamento voltou para análise.',
                'data' => $agendamentoAtualizado
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao atualizar o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $this->requireAuth();

        $agendamentos = $this->schedulingModel->allByClient($_SESSION['id_cliente']);

        $this->json([
            'success' => true,
            'data' => $agendamentos
        ]);
    }

    public function show()
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'ID inválido.'
            ], 400);
        }

        $agendamento = $this->schedulingModel->findByIdAndClient($id, $_SESSION['id_cliente']);

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

        $idCliente = $_SESSION['id_cliente'] ?? null;
        $idBarbeiro = (int)($input['barbeiro_id'] ?? 0);
        $servicosSelecionados = $input['servicos'] ?? [];
        $data = trim($input['data_agendamento'] ?? '');
        $hora = trim($input['hora_agendamento'] ?? '');
        $descricao = trim($input['descricao'] ?? '');

        if (empty($idCliente)) {
            $this->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        if (!is_array($servicosSelecionados)) {
            $servicosSelecionados = [$servicosSelecionados];
        }

        if ($idBarbeiro <= 0 || empty($servicosSelecionados) || $data === '' || $hora === '') {
            $this->json([
                'success' => false,
                'message' => 'Preencha barbeiro, serviço(s), data e horário.'
            ], 422);
        }

        $idCliente = (int) $idCliente;

        $servicosSelecionados = array_map('intval', $servicosSelecionados);
        $servicosSelecionados = array_values(array_filter($servicosSelecionados));

        if (empty($servicosSelecionados)) {
            $this->json([
                'success' => false,
                'message' => 'Selecione pelo menos um serviço válido.'
            ], 422);
        }

        if (!$this->clientModel->find($idCliente)) {
            $this->json([
                'success' => false,
                'message' => 'Cliente da sessão inválido.'
            ], 422);
        }

        if (!$this->serviceModel->barberHasAllServices($idBarbeiro, $servicosSelecionados)) {
            $this->json([
                'success' => false,
                'message' => 'O barbeiro selecionado não atende todos os serviços escolhidos.'
            ], 422);
        }

        $dataHora = $data . ' ' . $hora . ':00';

        $timestamp = strtotime($dataHora);

        if (!$timestamp) {
            $this->json([
                'success' => false,
                'message' => 'Data ou horário inválido.'
            ], 422);
        }

        $dataHora = date('Y-m-d H:i:s', $timestamp);

        if (!$this->schedulingModel->isTimeAvailable($idBarbeiro, $dataHora, $servicosSelecionados)) {
            $this->json([
                'success' => false,
                'message' => 'Esse horário não está mais disponível para o barbeiro selecionado.'
            ], 409);
        }

        $dados = [
            'id_cliente' => $idCliente,
            'id_barbeiro' => $idBarbeiro,
            'data_hora' => $dataHora,
            'descricao' => $descricao,
            'status' => 'pendente'
        ];

        try {
            $novoId = $this->schedulingModel->create($dados, $servicosSelecionados);

            $this->json([
                'success' => true,
                'message' => 'Agendamento criado com sucesso.',
                'id' => $novoId
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->json([
                    'success' => false,
                    'message' => 'Esse barbeiro já possui agendamento nesse horário.'
                ], 409);
            }

            $this->json([
                'success' => false,
                'message' => 'Erro ao salvar agendamento.',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao salvar agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->requireAuth();

            $id = $id ?? ($_GET['id'] ?? null);
            $idCliente = $_SESSION['id_cliente'] ?? null;

            $id = (int) $id;
            $idCliente = (int) $idCliente;

            if ($id <= 0) {
                $this->json([
                    'success' => false,
                    'message' => 'ID inválido.'
                ], 400);
            }

            if ($idCliente <= 0) {
                $this->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $ok = $this->schedulingModel->cancelByIdAndClient($id, $idCliente);

            if (!$ok) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado, não pertence ao usuário logado ou não pode mais ser cancelado.'
                ], 404);
            }

            $this->json([
                'success' => true,
                'message' => 'Agendamento cancelado com sucesso.'
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao cancelar o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getAvailableTimes()
    {
        try {
            $this->requireAuth();

            $idBarbeiro = (int)($_GET['id_barbeiro'] ?? 0);
            $data = trim($_GET['data_agendamento'] ?? '');
            $servicos = $_GET['servicos'] ?? [];

            if (!is_array($servicos)) {
                $servicos = [$servicos];
            }

            $servicos = array_map('intval', $servicos);
            $servicos = array_values(array_filter($servicos));

            if ($idBarbeiro <= 0 || $data === '' || empty($servicos)) {
                $this->json([
                    'success' => false,
                    'message' => 'Informe barbeiro, serviços e data para buscar horários.'
                ], 422);
            }

            if (!$this->serviceModel->barberHasAllServices($idBarbeiro, $servicos)) {
                $this->json([
                    'success' => false,
                    'message' => 'O barbeiro selecionado não atende todos os serviços escolhidos.'
                ], 422);
            }

            $horarios = $this->schedulingModel->getAvailableTimes(
                $idBarbeiro,
                $data,
                $servicos,
                '08:00',
                '17:00',
                30
            );

            $this->json([
                'success' => true,
                'message' => 'Horários disponíveis encontrados.',
                'data' => $horarios
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao buscar horários disponíveis.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function demoUpdate($id = null)
    {
        try {
            $id = $id ?? ($_GET['id'] ?? null);
            $id = (int) $id;

            if ($id <= 0) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento inválido.'
                ], 400);
            }

            $agendamento = $this->schedulingModel->getById($id);

            if (!$agendamento) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado.'
                ], 404);
            }

            if (in_array($agendamento['status'], ['cancelado', 'concluido'], true)) {
                $this->json([
                    'success' => false,
                    'message' => 'Este agendamento não pode mais ser alterado.'
                ], 422);
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input) || empty($input)) {
                $this->json([
                    'success' => false,
                    'message' => 'Nenhum dado foi enviado para atualização.'
                ], 400);
            }

            $dados = [];
            $servicosSelecionados = null;

            if (isset($input['servicos'])) {
                $servicosSelecionados = $input['servicos'];

                if (!is_array($servicosSelecionados)) {
                    $servicosSelecionados = [$servicosSelecionados];
                }

                $servicosSelecionados = array_map('intval', $servicosSelecionados);
                $servicosSelecionados = array_values(array_filter($servicosSelecionados));

                if (empty($servicosSelecionados)) {
                    $this->json([
                        'success' => false,
                        'message' => 'Selecione pelo menos um serviço.'
                    ], 422);
                }

                if (!$this->serviceModel->barberHasAllServices((int)$agendamento['id_barbeiro'], $servicosSelecionados)) {
                    $this->json([
                        'success' => false,
                        'message' => 'O barbeiro selecionado não atende todos os serviços escolhidos.'
                    ], 422);
                }
            }

            $servicosParaValidar = $servicosSelecionados;

            if ($servicosParaValidar === null) {
                $servicosAtuais = $this->schedulingModel->getServicesBySchedulingId($id);

                $servicosParaValidar = [];

                foreach ($servicosAtuais as $servicoAtual) {
                    $servicosParaValidar[] = (int) $servicoAtual['id_servico'];
                }
            }

            if (empty($servicosParaValidar)) {
                $this->json([
                    'success' => false,
                    'message' => 'Não foi possível identificar os serviços do agendamento.'
                ], 422);
            }

            $novaDataHora = null;

            if (!empty($input['data_hora'])) {
                $novaDataHora = trim($input['data_hora']);
            } elseif (!empty($input['data_agendamento']) && !empty($input['hora_agendamento'])) {
                $data = trim($input['data_agendamento']);
                $hora = trim($input['hora_agendamento']);

                $novaDataHora = $data . ' ' . $hora . ':00';
            }

            if ($novaDataHora !== null) {
                $timestamp = strtotime($novaDataHora);

                if (!$timestamp) {
                    $this->json([
                        'success' => false,
                        'message' => 'Data ou horário inválido.'
                    ], 422);
                }

                $novaDataHora = date('Y-m-d H:i:s', $timestamp);

                if (!$this->schedulingModel->isTimeAvailable(
                    (int) $agendamento['id_barbeiro'],
                    $novaDataHora,
                    $servicosParaValidar,
                    $id
                )) {
                    $this->json([
                        'success' => false,
                        'message' => 'Esse horário não está disponível para o barbeiro selecionado.'
                    ], 409);
                }

                $dados['data_hora'] = $novaDataHora;
            }

            if (array_key_exists('descricao', $input)) {
                $dados['descricao'] = trim($input['descricao']);
            }

            if (array_key_exists('status', $input)) {
                $dados['status'] = trim($input['status']);
            } elseif (in_array($agendamento['status'], ['agendado', 'checked'], true)) {
                $dados['status'] = 'pendente';
            }

            if (empty($dados) && $servicosSelecionados === null) {
                $this->json([
                    'success' => false,
                    'message' => 'Nenhum dado válido foi enviado para atualização.'
                ], 400);
            }

            $this->schedulingModel->updateScheduleChange(
                $id,
                $dados,
                $servicosSelecionados
            );

            $agendamentoAtualizado = $this->schedulingModel->getById($id);

            $this->json([
                'success' => true,
                'message' => 'Agendamento demo atualizado com sucesso.',
                'data' => $agendamentoAtualizado
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao atualizar agendamento demo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    public function demoPost()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input)) {
                $this->json([
                    'success' => false,
                    'message' => 'JSON inválido.'
                ], 400);
            }

            $idCliente = (int)($input['id_cliente'] ?? 0);
            $idBarbeiro = (int)($input['barbeiro_id'] ?? 0);
            $servicosSelecionados = $input['servicos'] ?? [];
            $data = trim($input['data_agendamento'] ?? '');
            $hora = trim($input['hora_agendamento'] ?? '');
            $descricao = trim($input['descricao'] ?? 'Agendamento criado pela rota demo.');

            if (!is_array($servicosSelecionados)) {
                $servicosSelecionados = [$servicosSelecionados];
            }

            if ($idCliente <= 0 || $idBarbeiro <= 0 || empty($servicosSelecionados) || $data === '' || $hora === '') {
                $this->json([
                    'success' => false,
                    'message' => 'Informe id_cliente, barbeiro_id, serviço(s), data e horário.'
                ], 422);
            }

            $servicosSelecionados = array_map('intval', $servicosSelecionados);
            $servicosSelecionados = array_values(array_filter($servicosSelecionados));

            if (empty($servicosSelecionados)) {
                $this->json([
                    'success' => false,
                    'message' => 'Selecione pelo menos um serviço válido.'
                ], 422);
            }

            if (!$this->clientModel->find($idCliente)) {
                $this->json([
                    'success' => false,
                    'message' => 'Cliente informado não existe.'
                ], 422);
            }

            if (!$this->serviceModel->barberHasAllServices($idBarbeiro, $servicosSelecionados)) {
                $this->json([
                    'success' => false,
                    'message' => 'O barbeiro selecionado não atende todos os serviços escolhidos.'
                ], 422);
            }

            $dataHora = $data . ' ' . $hora . ':00';

            $timestamp = strtotime($dataHora);

            if (!$timestamp) {
                $this->json([
                    'success' => false,
                    'message' => 'Data ou horário inválido.'
                ], 422);
            }

            $dataHora = date('Y-m-d H:i:s', $timestamp);

            if (!$this->schedulingModel->isTimeAvailable($idBarbeiro, $dataHora, $servicosSelecionados)) {
                $this->json([
                    'success' => false,
                    'message' => 'Esse horário não está disponível para o barbeiro selecionado.'
                ], 409);
            }

            $dados = [
                'id_cliente' => $idCliente,
                'id_barbeiro' => $idBarbeiro,
                'data_hora' => $dataHora,
                'descricao' => $descricao,
                'status' => 'pendente'
            ];

            $novoId = $this->schedulingModel->create($dados, $servicosSelecionados);

            $this->json([
                'success' => true,
                'message' => 'Agendamento demo criado com sucesso.',
                'id' => $novoId,
                'data' => [
                    'id_cliente' => $idCliente,
                    'id_barbeiro' => $idBarbeiro,
                    'servicos' => $servicosSelecionados,
                    'data_hora' => $dataHora,
                    'descricao' => $descricao,
                    'status' => 'pendente'
                ]
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->json([
                    'success' => false,
                    'message' => 'Esse barbeiro já possui agendamento nesse horário.'
                ], 409);
            }

            $this->json([
                'success' => false,
                'message' => 'Erro ao criar agendamento demo.',
                'error' => $e->getMessage()
            ], 500);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao criar agendamento demo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function demoGetById($id = null)
    {
        try {
            $id = $id ?? ($_GET['id'] ?? null);

            if (!$id) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento não informado.'
                ], 400);
            }

            $id = (int) $id;

            if ($id <= 0) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento inválido.'
                ], 400);
            }

            $agendamento = $this->schedulingModel->getById($id);

            if (!$agendamento) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado.'
                ], 404);
            }

            $this->json([
                'success' => true,
                'message' => 'Agendamento encontrado com sucesso.',
                'data' => $agendamento
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao buscar o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function demoDeleteById($id = null)
    {
        try {
            $this->requireAuth();

            $id = $id ?? ($_GET['id'] ?? null);
            $id = (int) $id;

            if ($id <= 0) {
                $this->json([
                    'success' => false,
                    'message' => 'ID do agendamento inválido.'
                ], 400);
            }

            $ok = $this->schedulingModel->deleteById($id);

            if (!$ok) {
                $this->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado ou já foi excluído.'
                ], 404);
            }

            $this->json([
                'success' => true,
                'message' => 'Agendamento excluído definitivamente com sucesso.'
            ], 200);

        } catch (Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao excluir o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}