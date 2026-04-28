<?php

require_once __DIR__ . '/../models/Client.php';

class AuthController
{
    private $clientModel;

    public function __construct($pdo)
    {
        $this->clientModel = new Client($pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function renderLogin(?string $errorMessage = null, array $old = [])
    {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        if (!empty($_SESSION['id_cliente'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $this->renderLogin();
    }

    public function authenticate()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        $old = [
            'email' => $email
        ];

        if ($email === '' || $senha === '') {
            $this->renderLogin('Preencha email e senha.', $old);
            return;
        }

        $cliente = $this->clientModel->findByEmail($email);

        if (!$cliente) {
            $this->renderLogin('Email ou senha inválidos.', $old);
            return;
        }

        $senhaBanco = $cliente['senha'] ?? '';

        if (!password_verify($senha, $senhaBanco)) {
            $this->renderLogin('Email ou senha inválidos.', $old);
            return;
        }

        $_SESSION['id_cliente'] = $cliente['id_cliente'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        header('Location: index.php?action=home');
        exit;
    }

    public function home()
    {
        if (empty($_SESSION['id_cliente'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $clienteNome = $_SESSION['cliente_nome'] ?? 'Usuário';

        require __DIR__ . '/../views/home/index.php';
    }
}