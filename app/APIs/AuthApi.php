<?php
require_once __DIR__ . '/BaseApi.php';
require_once __DIR__ . '/../models/Client.php';


class AuthApi extends BaseApi
{
    private $clientModel;

    public function __construct($pdo)
    {
        $this->clientModel = new Client($pdo);
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
}