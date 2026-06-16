<?php

require_once __DIR__ . '/../models/Barber.php';

class BarberApi
{
    private $pdo;

    public function __construct()
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->pdo = $this->getDatabaseConnection();
    }

    private function getDatabaseConnection()
    {
        require __DIR__ . '/../../config/database.php';

        if (isset($pdo) && $pdo instanceof PDO) {
            return $pdo;
        }

        throw new Exception('Variável $pdo não encontrada no arquivo database.php.');
    }

    private function getJsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            return $_POST;
        }

        return $input;
    }

    private function response($success, $message, $data = null, $httpCode = 200)
    {
        http_response_code($httpCode);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    private function getIdFromRequest($input = [])
    {
        $id = $_GET['id'] ?? $input['id_barbeiro'] ?? null;

        if (!$id || !is_numeric($id)) {
            $this->response(false, 'ID do barbeiro é obrigatório.', null, 400);
        }

        return (int) $id;
    }

    public function index()
    {
        try {
            $barberModel = new Barber($this->pdo);
            $barbers = $barberModel->all();

            $this->response(true, 'Barbeiros encontrados.', $barbers);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao listar barbeiros.', null, 500);
        }
    }

    public function show()
    {
        try {
            $id = $this->getIdFromRequest();

            $barberModel = new Barber($this->pdo);
            $barber = $barberModel->findWithServices($id);

            if (!$barber) {
                $this->response(false, 'Barbeiro não encontrado.', null, 404);
            }

            $this->response(true, 'Barbeiro encontrado.', $barber);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao buscar barbeiro.', null, 500);
        }
    }

    public function getServicesByBarber()
    {
        try {
            $id = $this->getIdFromRequest();

            $barberModel = new Barber($this->pdo);

            $barber = $barberModel->find($id);

            if (!$barber) {
                $this->response(false, 'Barbeiro não encontrado.', null, 404);
            }

            $servicos = $barberModel->getServicesByBarber($id);

            $this->response(true, 'Serviços do barbeiro encontrados.', [
                'barbeiro' => $barber,
                'servicos' => $servicos
            ]);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao buscar serviços do barbeiro.', null, 500);
        }
    }

    public function store()
    {
        try {
            $input = $this->getJsonInput();

            $nome = trim($input['nome'] ?? '');
            $email = array_key_exists('email', $input) ? trim($input['email']) : null;
            $senha = trim($input['senha'] ?? '');
            $servicos = $input['servicos'] ?? [];

            if (empty($nome)) {
                $this->response(false, 'O nome do barbeiro é obrigatório.', null, 400);
            }

            if (empty($email)) {
                $this->response(false, 'O e-mail do barbeiro é obrigatório.', null, 400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->response(false, 'Informe um e-mail válido para o barbeiro.', null, 400);
            }

            if (empty($senha)) {
                $this->response(false, 'A senha do barbeiro é obrigatória.', null, 400);
            }

            if (strlen($senha) < 5) {
                $this->response(false, 'A senha deve ter pelo menos 5 caracteres.', null, 400);
            }

            if (!is_array($servicos)) {
                $this->response(false, 'O campo servicos deve ser uma lista de IDs.', null, 400);
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $barberModel = new Barber($this->pdo);

            $idBarbeiro = $barberModel->create($nome, $email, $senhaHash, $servicos);

            $barber = $barberModel->findWithServices($idBarbeiro);

            $this->response(true, 'Barbeiro cadastrado com sucesso.', $barber, 201);

        } catch (InvalidArgumentException $e) {
            $this->response(false, $e->getMessage(), null, 400);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->response(false, 'Este e-mail já está cadastrado para outro barbeiro.', null, 400);
            }

            $this->response(false, 'Erro no banco de dados ao cadastrar barbeiro.', null, 500);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao cadastrar barbeiro.', null, 500);
        }
    }

    public function update()
    {
        try {
            $input = $this->getJsonInput();

            $id = $this->getIdFromRequest($input);

            $nome = null;
            $email = null;
            $senhaHash = null;
            $servicos = null;

            if (array_key_exists('nome', $input)) {
                $nome = trim($input['nome']);

                if (empty($nome)) {
                    $this->response(false, 'O nome do barbeiro não pode ser vazio.', null, 400);
                }
            }

            if (array_key_exists('email', $input)) {
                $email = trim($input['email']);
            }

            if (array_key_exists('senha', $input)) {
                $senha = trim($input['senha']);

                if ($senha !== '') {
                    if (strlen($senha) < 5) {
                        $this->response(false, 'A nova senha deve ter pelo menos 5 caracteres.', null, 400);
                    }

                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                }
            }

            if (array_key_exists('servicos', $input)) {
                if (!is_array($input['servicos'])) {
                    $this->response(false, 'O campo servicos deve ser uma lista de IDs.', null, 400);
                }

                $servicos = $input['servicos'];
            }

            if ($nome === null && $email === null && $senhaHash === null && $servicos === null) {
                $this->response(false, 'Informe pelo menos um campo para atualizar.', null, 400);
            }

            $barberModel = new Barber($this->pdo);

            $updated = $barberModel->update($id, $nome, $email, $servicos, $senhaHash);

            if (!$updated) {
                $this->response(false, 'Barbeiro não encontrado.', null, 404);
            }

            $barber = $barberModel->findWithServices($id);

            $this->response(true, 'Barbeiro atualizado com sucesso.', $barber);

        } catch (InvalidArgumentException $e) {
            $this->response(false, $e->getMessage(), null, 400);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->response(false, 'Este e-mail já está cadastrado para outro barbeiro.', null, 400);
            }

            $this->response(false, 'Erro no banco de dados ao atualizar barbeiro.', null, 500);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao atualizar barbeiro.', null, 500);
        }
    }

    public function updateServices()
    {
        try {
            $input = $this->getJsonInput();

            $id = $this->getIdFromRequest($input);
            $servicos = $input['servicos'] ?? null;

            if (!is_array($servicos)) {
                $this->response(false, 'O campo servicos deve ser uma lista de IDs.', null, 400);
            }

            $barberModel = new Barber($this->pdo);

            $barber = $barberModel->find($id);

            if (!$barber) {
                $this->response(false, 'Barbeiro não encontrado.', null, 404);
            }

            $barberModel->syncServices($id, $servicos);

            $barberUpdated = $barberModel->findWithServices($id);

            $this->response(true, 'Serviços do barbeiro atualizados com sucesso.', $barberUpdated);

        } catch (InvalidArgumentException $e) {
            $this->response(false, $e->getMessage(), null, 400);

        } catch (PDOException $e) {
            $this->response(false, 'Erro no banco de dados ao atualizar serviços do barbeiro.', null, 500);

        } catch (Exception $e) {
            $this->response(false, 'Erro ao atualizar serviços do barbeiro.', null, 500);
        }
    }

    public function delete()
    {
        try {
            $input = $this->getJsonInput();

            $id = $this->getIdFromRequest($input);

            $barberModel = new Barber($this->pdo);

            $result = $barberModel->delete($id);

            if (!$result['success']) {
                $httpCode = 400;

                if ($result['reason'] === 'not_found') {
                    $httpCode = 404;
                }

                $this->response(
                    false,
                    $result['message'],
                    null,
                    $httpCode
                );
            }

            $message = 'Barbeiro excluído com sucesso.';

            if (
                isset($result['reassigned_scheduling_count']) &&
                $result['reassigned_scheduling_count'] > 0 &&
                isset($result['new_barber']) &&
                $result['new_barber']
            ) {
                $message = 'Barbeiro excluído com sucesso. Os agendamentos foram redirecionados para ' .
                    $result['new_barber']['nome'] .
                    ' e voltaram para o status pendente.';
            }

            $this->response(true, $message, $result);

        } catch (PDOException $e) {
            $this->response(
                false,
                'Erro no banco de dados ao excluir barbeiro.',
                null,
                500
            );

        } catch (Exception $e) {
            $this->response(false, 'Erro ao excluir barbeiro.', null, 500);
        }
    }
}