<?php
require_once __DIR__ . '/BaseApi.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Barber.php';

class AuthApi extends BaseApi
{
    private $clientModel;
    private $barberModel;

    public function __construct($pdo)
    {
        $this->clientModel = new Client($pdo);
        $this->barberModel = new Barber($pdo);
    }

    public function login()
    {
        $this->startSessionIfNeeded();

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            $this->json([
                'success' => false,
                'message' => 'JSON inválido.'
            ], 400);
        }

        $email = trim($input['email'] ?? '');
        $senha = trim($input['senha'] ?? '');

        if ($email === '' || $senha === '') {
            $this->json([
                'success' => false,
                'message' => 'Preencha email e senha.'
            ], 422);
        }

        $cliente = $this->clientModel->findByEmail($email);

        if (!$cliente) {
            $this->json([
                'success' => false,
                'message' => 'Email ou senha inválidos.'
            ], 401);
        }

        $senhaBanco = $cliente['senha'] ?? '';

        if (!password_verify($senha, $senhaBanco)) {
            $this->json([
                'success' => false,
                'message' => 'Email ou senha inválidos.'
            ], 401);
        }

        session_regenerate_id(true);

        $_SESSION['id_cliente'] = $cliente['id_cliente'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        $this->json([
            'success' => true,
            'message' => 'Login realizado com sucesso.',
            'user' => [
                'id_cliente' => $cliente['id_cliente'],
                'nome' => $cliente['nome'],
                'email' => $cliente['email']
            ]
        ]);
    }

    public function me()
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['id_cliente'])) {
            $this->json([
                'authenticated' => false
            ]);
        }

        $this->json([
            'authenticated' => true,
            'user' => [
                'id_cliente' => $_SESSION['id_cliente'],
                'nome' => $_SESSION['cliente_nome'] ?? '',
                'email' => $_SESSION['cliente_email'] ?? ''
            ]
        ]);
    }

    public function logout()
    {
        $this->startSessionIfNeeded();

        $_SESSION = [];
        session_unset();

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

        $this->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso.'
        ]);
    }


    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->json([
                'success' => false,
                'message' => 'Método não permitido.'
            ], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            $this->json([
                'success' => false,
                'message' => 'JSON inválido.'
            ], 400);
        }

        $email = trim($input['email'] ?? '');
        $senha = trim($input['senha'] ?? '');
        $confirmarSenha = trim($input['confirmar_senha'] ?? '');

        if ($email === '') {
            $this->json([
                'success' => false,
                'message' => 'E-mail obrigatório.'
            ], 422);
        }

        if ($senha === '') {
            $this->json([
                'success' => false,
                'message' => 'A nova senha é obrigatória.'
            ], 422);
        }

        if (strlen($senha) < 6) {
            $this->json([
                'success' => false,
                'message' => 'A senha deve ter pelo menos 6 caracteres.'
            ], 422);
        }

        if ($confirmarSenha === '') {
            $this->json([
                'success' => false,
                'message' => 'A confirmação de senha é obrigatória.'
            ], 422);
        }

        if ($senha !== $confirmarSenha) {
            $this->json([
                'success' => false,
                'message' => 'As senhas não coincidem.'
            ], 422);
        }

        $cliente = $this->clientModel->findByEmail($email);

        if (!$cliente) {
            $this->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sucesso = $this->clientModel->updatePassword($cliente['id_cliente'], $senhaHash);

        if (!$sucesso) {
            $this->json([
                'success' => false,
                'message' => 'Não foi possível redefinir a senha.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Senha redefinida com sucesso.'
        ]);
    }


    public function loginBarber()
    {
        $this->startSessionIfNeeded();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Método não permitido.'
            ], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            $this->json([
                'success' => false,
                'message' => 'JSON inválido.'
            ], 400);
        }

        $email = trim($input['email'] ?? '');
        $senha = trim($input['senha'] ?? '');

        if ($email === '' || $senha === '') {
            $this->json([
                'success' => false,
                'message' => 'Preencha email e senha.'
            ], 422);
        }

        $barbeiro = $this->barberModel->findByEmail($email);

        if (!$barbeiro) {
            $this->json([
                'success' => false,
                'message' => 'Email ou senha inválidos.'
            ], 401);
        }

        $senhaBanco = $barbeiro['senha'] ?? '';

        if (!password_verify($senha, $senhaBanco)) {
            $this->json([
                'success' => false,
                'message' => 'Email ou senha inválidos.'
            ], 401);
        }

        session_regenerate_id(true);

        unset(
            $_SESSION['id_cliente'],
            $_SESSION['cliente_nome'],
            $_SESSION['cliente_email']
        );

        $_SESSION['id_barbeiro'] = $barbeiro['id_barbeiro'];
        $_SESSION['barbeiro_nome'] = $barbeiro['nome'];
        $_SESSION['barbeiro_email'] = $barbeiro['email'];
        $_SESSION['tipo_usuario'] = 'barbeiro';

        $this->json([
            'success' => true,
            'message' => 'Login de barbeiro realizado com sucesso.',
            'user' => [
                'id_barbeiro' => $barbeiro['id_barbeiro'],
                'nome' => $barbeiro['nome'],
                'email' => $barbeiro['email'],
                'tipo_usuario' => 'barbeiro'
            ],
            'redirect' => 'index.php?action=review_pending'
        ]);
    }


    public function barberMe()
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['id_barbeiro'])) {
            $this->json([
                'authenticated' => false
            ]);
        }

        $this->json([
            'authenticated' => true,
            'user' => [
                'id_barbeiro' => $_SESSION['id_barbeiro'],
                'nome' => $_SESSION['barbeiro_nome'] ?? '',
                'email' => $_SESSION['barbeiro_email'] ?? '',
                'tipo_usuario' => $_SESSION['tipo_usuario'] ?? 'barbeiro'
            ]
        ]);
    }


}