<?php

require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Scheduling.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $clientModel;
    private $schedulingModel;
    private $userModel;

    public function __construct($pdo)
    {
        $this->clientModel = new Client($pdo);
        $this->schedulingModel = new Scheduling($pdo);
        $this->userModel = new User($pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function renderLogin(?string $errorMessage = null, array $old = [])
    {
        $errors = [];
        $data = $old;

        if ($errorMessage !== null) {
            $errors['geral'] = $errorMessage;
        }

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

    public function forgotPassword(array $errors = [], array $data = [])
    {
        require __DIR__ . '/../views/auth/forgot-password.php';
    }

    public function handleForgotPassword()
    {
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        $data = ['email' => $email];

        if ($email === '') {
            $errors['email'] = 'O e-mail é obrigatório.';
            $this->forgotPassword($errors, $data);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
            $this->forgotPassword($errors, $data);
            return;
        }

        $cliente = $this->clientModel->findByEmail($email);

        if (!$cliente) {
            $errors['email'] = 'Nenhum usuário encontrado com esse e-mail.';
            $this->forgotPassword($errors, $data);
            return;
        }

        header('Location: index.php?action=reset_password_form&email=' . urlencode($email));
        exit;
    }

    public function resetPasswordForm(array $errors = [])
    {
        $email = $_GET['email'] ?? '';

        if ($email === '') {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        require __DIR__ . '/../views/auth/reset-password.php';
    }

    public function resetPassword()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

        $errors = [];

        if ($email === '') {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        if ($senha === '') {
            $errors['senha'] = 'A nova senha é obrigatória.';
        } elseif (strlen($senha) < 6) {
            $errors['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
        }

        if ($confirmarSenha === '') {
            $errors['confirmar_senha'] = 'A confirmação de senha é obrigatória.';
        } elseif ($senha !== $confirmarSenha) {
            $errors['confirmar_senha'] = 'As senhas não coincidem.';
        }

        $cliente = $this->clientModel->findByEmail($email);

        if (!$cliente) {
            $errors['geral'] = 'Usuário não encontrado.';
        }

        if (!empty($errors)) {
            $_GET['email'] = $email;
            $this->resetPasswordForm($errors);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sucesso = $this->userModel->updatePassword($cliente['id_cliente'], $senhaHash);

        if ($sucesso) {
            header('Location: index.php?action=login');
            exit;
        }

        $errors['geral'] = 'Não foi possível redefinir a senha.';
        $_GET['email'] = $email;
        $this->resetPasswordForm($errors);
    }

    public function home()
    {
        if (empty($_SESSION['id_cliente'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $clienteNome = $_SESSION['cliente_nome'] ?? 'Usuário';
        $proximoAgendamento = $this->schedulingModel->getNextByClient($_SESSION['id_cliente']);

        require __DIR__ . '/../views/home/index.php';
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?action=login');
        exit;
    }
}