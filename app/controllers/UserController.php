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
            header('Location: index.php?action=user_create&success=1');
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

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $emailFinal = ($email === '') ? null : $email;

        try {
            $sucesso = $this->userModel->create($nome, $telefone, $emailFinal, $senhaHash);

            if ($sucesso) {
                header('Location: index.php?action=user_create&success=1');
                exit;
            } else {
                $errors['geral'] = 'Não foi possível cadastrar o cliente.';
                $this->create($data, $errors);
            }

        } catch (PDOException $e) {
            $errors['geral'] = 'Erro ao salvar no banco: ' . $e->getMessage();
            $this->create($data, $errors);
        }
    }
}