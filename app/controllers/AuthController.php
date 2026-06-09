<?php

require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Barber.php';
require_once __DIR__ . '/../models/Scheduling.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $pdo;
    private $clientModel;
    private $barberModel;
    private $schedulingModel;
    private $userModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->clientModel = new Client($pdo);
        $this->barberModel = new Barber($pdo);
        $this->schedulingModel = new Scheduling($pdo);
        $this->userModel = new User($pdo);
    }

    private function startSessionIfNeeded()
    {
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
        if (!empty($_COOKIE[session_name()])) {
            $this->startSessionIfNeeded();
        }

        if (!empty($_SESSION['id_cliente'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $this->renderLogin();
    }

    public function authenticate()
    {
        header('Location: index.php?action=login');
        exit;
    }

    private function getModelByRole(string $role)
    {
        return $role === 'barber' ? $this->barberModel : $this->clientModel;
    }

    public function forgotPassword(array $errors = [], array $data = [], string $role = 'client')
    {
        $data['role'] = $role;
        require __DIR__ . '/../views/auth/forgot-password.php';
    }

    public function barberForgotPassword()
    {
        $this->forgotPassword([], ['role' => 'barber'], 'barber');
    }

    public function handleForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'client');
        $role = $role === 'barber' ? 'barber' : 'client';

        $data = [
            'email' => $email,
            'role' => $role
        ];

        $errors = [];

        if ($email === '') {
            $errors['email'] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
        }

        if (!empty($errors)) {
            $this->forgotPassword($data, $errors, $role);
            return;
        }

        $model = $this->getModelByRole($role);
        $account = $model->findByEmail($email);

        if (!$account) {
            $errors['email'] = 'Nenhuma conta foi encontrada com este e-mail.';
            $this->forgotPassword($data, $errors, $role);
            return;
        }

        $this->startSessionIfNeeded();

        $_SESSION['reset_email'] = $account['email'];
        $_SESSION['reset_role'] = $role;

        $redirectAction = $role === 'barber' ? 'barber_reset_password_form' : 'reset_password_form';

        header('Location: index.php?action=' . $redirectAction);
        exit;
    }

    public function resetPasswordForm(array $errors = [])
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['reset_email'])) {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        $role = $_SESSION['reset_role'] ?? 'client';

        require __DIR__ . '/../views/auth/reset-password.php';
    }

    public function resetPassword()
    {
        $this->startSessionIfNeeded();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        if (empty($_SESSION['reset_email'])) {
            header('Location: index.php?action=forgot_password');
            exit;
        }

        $email = $_SESSION['reset_email'];

        $senha = trim($_POST['senha'] ?? '');
        $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

        $errors = [];

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

        if (!empty($errors)) {
            $this->resetPasswordForm($errors);
            return;
        }

        try {
            $payload = [
                'email' => $email,
                'senha' => $senha,
                'confirmar_senha' => $confirmarSenha
            ];

            $role = $_SESSION['reset_role'] ?? 'client';
            $apiAction = $role === 'barber' ? 'api_barber_reset_password' : 'api_auth_reset_password';
            $apiUrl = $this->getBaseUrl() . '/index.php?action=' . $apiAction;

            $context = stream_context_create([
                'http' => [
                    'method' => 'PUT',
                    'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'ignore_errors' => true
                ]
            ]);

            $apiResponse = file_get_contents($apiUrl, false, $context);

            if ($apiResponse === false) {
                $errors['geral'] = 'Não foi possível conectar com a API de autenticação.';
                $this->resetPasswordForm($errors);
                return;
            }

            $result = json_decode($apiResponse, true);

            if (!is_array($result)) {
                $errors['geral'] = 'A API retornou uma resposta inválida.';
                $this->resetPasswordForm($errors);
                return;
            }

            if (empty($result['success'])) {
                $errors['geral'] = $result['message'] ?? 'Não foi possível redefinir a senha.';
                $this->resetPasswordForm($errors);
                return;
            }

            unset($_SESSION['reset_email'], $_SESSION['reset_role']);

            $redirect = $role === 'barber' ? 'index.php?action=barber_login' : 'index.php?action=login';
            header('Location: ' . $redirect);
            exit;

        } catch (Exception $e) {
            $errors['geral'] = 'Erro ao consumir a API: ' . $e->getMessage();
            $this->resetPasswordForm($errors);
            return;
        }
    }

    public function home()
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['id_cliente'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $clienteNome = $_SESSION['cliente_nome'] ?? 'Usuário';

        require __DIR__ . '/../views/home/index.php';
    }

    public function logout()
    {
        $this->startSessionIfNeeded();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header('Location: index.php?action=login');
        exit;
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