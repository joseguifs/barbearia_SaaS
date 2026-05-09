<?php

// 1. CONFIGURAÇÃO DE CAMINHOS
$appDir = dirname(__DIR__); // app
$rootDir = dirname($appDir); // barbearia_SaaS

require_once $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once $appDir . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Client.php';

// 2. CLASSE CONTROLADORA DA API
class ClientApiController
{
    private $pdo;
    private $model;

    public function __construct($pdo)
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->pdo = $pdo;
        $this->model = new Client($this->pdo);
    }

    private function response($success, $message, $data = null, $code = 200)
    {
        http_response_code($code);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id'] ?? null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $result = $this->model->find($id);
                        $result ? $this->response(true, "Cliente encontrado", $result) 
                                : $this->response(false, "Cliente não encontrado", null, 404);
                    } else {
                        $this->response(true, "Lista de clientes", $this->model->all());
                    }
                    break;

                case 'POST':
                    $input = json_decode(file_get_contents('php://input'), true);
                    if (empty($input['nome']) || empty($input['senha'])) {
                        $this->response(false, "Nome e senha são obrigatórios", null, 400);
                    }
                    // Hash da senha por segurança
                    $senhaHash = password_hash($input['senha'], PASSWORD_DEFAULT);
                    $this->model->create($input['nome'], $input['telefone'] ?? '', $input['email'] ?? null, $senhaHash);
                    $this->response(true, "Cliente cadastrado com sucesso", null, 201);
                    break;

                case 'PUT':
                    if (!$id) $this->response(false, "ID necessário", null, 400);
                    $input = json_decode(file_get_contents('php://input'), true);
                    $this->model->update($id, $input['nome'], $input['telefone'], $input['email']);
                    $this->response(true, "Cliente atualizado", null);
                    break;

                case 'DELETE':
                    if (!$id) $this->response(false, "ID necessário", null, 400);
                    $this->model->delete($id);
                    $this->response(true, "Cliente removido", null);
                    break;

                default:
                    $this->response(false, "Método não permitido", null, 405);
            }
        } catch (Exception $e) {
            $this->response(false, "Erro no servidor: " . $e->getMessage(), null, 500);
        }
    }
}

// 3. EXECUÇÃO IMEDIATA
// A variável $pdo vem do arquivo database.php incluído no topo
if (isset($pdo)) {
    $api = new ClientApiController($pdo);
    $api->handleRequest();
} else {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados.']);
}