<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function create($data = [], $errors = [])
    {
        require_once __DIR__ . '/../views/user/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=user_create');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        $data = [
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email
        ];

        $errors = [];

        if ($nome === '') {
            $errors['nome'] = 'O nome é obrigatório.';
        }

        if ($telefone === '') {
            $errors['telefone'] = 'O telefone é obrigatório.';
        }

        if ($email === '') {
            $errors['email'] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
        }

        if ($senha === '') {
            $errors['senha'] = 'A senha é obrigatória.';
        } elseif (strlen($senha) < 6) {
            $errors['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
        }

        if ($email !== '' && empty($errors['email'])) {
            $clienteExistente = $this->userModel->findByEmail($email);

            if ($clienteExistente) {
                $errors['email'] = 'Este e-mail já está cadastrado.';
            }
        }

        if (!empty($errors)) {
            $this->create($data, $errors);
            return;
        }

        try {
            $emailFinal = ($email === '') ? null : $email;

            $payload = [
                'nome' => $nome,
                'telefone' => $telefone,
                'email' => $emailFinal,
                'senha' => $senha
            ];

            $apiUrl = $this->getBaseUrl() . '/index.php?action=api_user_demo_get';

            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'ignore_errors' => true
                ]
            ]);

            $apiResponse = file_get_contents($apiUrl, false, $context);

            if ($apiResponse === false) {
                $errors['geral'] = 'Não foi possível conectar com a API de clientes.';
                $this->create($data, $errors);
                return;
            }

            $result = json_decode($apiResponse, true);

            if (!is_array($result)) {
                $errors['geral'] = 'A API retornou uma resposta inválida.';
                $this->create($data, $errors);
                return;
            }

            if (!empty($result['success'])) {
                header('Location: index.php?action=user_create&success=1');
                exit;
            }

            $errors['geral'] = $result['message'] ?? 'Não foi possível cadastrar o cliente.';
            $this->create($data, $errors);
            return;

        } catch (Exception $e) {
            $errors['geral'] = 'Erro ao consumir a API: ' . $e->getMessage();
            $this->create($data, $errors);
            return;
        }
    }

    private function getBaseUrl()
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';

        $host = $_SERVER['HTTP_HOST'];

        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $scriptDir = rtrim($scriptDir, '/');

        return $scheme . '://' . $host . $scriptDir;
    }
}
