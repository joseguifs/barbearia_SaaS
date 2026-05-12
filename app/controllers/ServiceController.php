<?php

require_once __DIR__ . '/../models/Service.php';

class ServiceController
{
    private $serviceModel;

    public function __construct($pdo)
    {
        $this->serviceModel = new Service($pdo);
    }

    public function index()
    {
        require_once __DIR__ . '/../views/services/index.php';
    }

    private function jsonResponse($success, $message, $data = null, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    private function getJsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : [];
    }

    private function normalizeServiceData(array $input)
    {
        $nome = trim($input['nome'] ?? '');
        $descricao = trim($input['descricao'] ?? '');
        $preco = $input['preco'] ?? '';
        $duracao = $input['duracao'] ?? '';

        if ($nome === '') {
            $this->jsonResponse(false, 'Informe o nome do serviço.', null, 400);
        }

        if ($preco === '' || !is_numeric($preco) || (float)$preco < 0) {
            $this->jsonResponse(false, 'Informe um preço válido.', null, 400);
        }

        if ($duracao === '' || !is_numeric($duracao) || (int)$duracao <= 0) {
            $this->jsonResponse(false, 'Informe uma duração válida em minutos.', null, 400);
        }

        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => number_format((float)$preco, 2, '.', ''),
            'duracao' => (int)$duracao
        ];
    }

    public function apiIndex()
    {
        try {
            $servicos = $this->serviceModel->all();
            $this->jsonResponse(true, 'Serviços listados com sucesso.', $servicos);
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao listar serviços.', null, 500);
        }
    }

    public function apiStore()
    {
        try {
            $dados = $this->normalizeServiceData($this->getJsonInput());
            $this->serviceModel->create($dados);

            $this->jsonResponse(true, 'Serviço cadastrado com sucesso.', null, 201);
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao cadastrar serviço.', null, 500);
        }
    }

    public function apiUpdate()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->jsonResponse(false, 'ID do serviço não informado.', null, 400);
        }

        try {
            $servico = $this->serviceModel->find((int)$id);

            if (!$servico) {
                $this->jsonResponse(false, 'Serviço não encontrado.', null, 404);
            }

            $dados = $this->normalizeServiceData($this->getJsonInput());
            $this->serviceModel->update((int)$id, $dados);

            $this->jsonResponse(true, 'Serviço atualizado com sucesso.');
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao atualizar serviço.', null, 500);
        }
    }

    public function apiDelete()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->jsonResponse(false, 'ID do serviço não informado.', null, 400);
        }

        try {
            $servico = $this->serviceModel->find((int)$id);

            if (!$servico) {
                $this->jsonResponse(false, 'Serviço não encontrado.', null, 404);
            }

            $this->serviceModel->delete((int)$id);

            $this->jsonResponse(true, 'Serviço excluído com sucesso.');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->jsonResponse(false, 'Não é possível excluir este serviço porque ele está vinculado a agendamentos.', null, 409);
            }

            $this->jsonResponse(false, 'Erro ao excluir serviço.', null, 500);
        }
    }
}
