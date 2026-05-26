<?php

class ProfileController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getLoggedClientId()
    {
        $this->startSessionIfNeeded();

        if (isset($_SESSION['id_cliente'])) {
            return (int) $_SESSION['id_cliente'];
        }

        if (isset($_SESSION['cliente']['id_cliente'])) {
            return (int) $_SESSION['cliente']['id_cliente'];
        }

        if (isset($_SESSION['user']['id_cliente'])) {
            return (int) $_SESSION['user']['id_cliente'];
        }

        if (isset($_SESSION['cliente_id'])) {
            return (int) $_SESSION['cliente_id'];
        }

        return null;
    }

    private function requireAuth()
    {
        $idCliente = $this->getLoggedClientId();

        if (!$idCliente) {
            header('Location: index.php?action=login');
            exit;
        }

        return $idCliente;
    }

    private function getClientById($idCliente)
    {
        $sql = "SELECT id_cliente, nome, telefone, email
                FROM cliente
                WHERE id_cliente = :id_cliente
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function existsDuplicate($field, $value, $idCliente)
    {
        $allowedFields = ['nome', 'email'];

        if (!in_array($field, $allowedFields, true)) {
            return false;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM cliente
                WHERE LOWER($field) = LOWER(:value)
                AND id_cliente <> :id_cliente";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'] > 0;
    }

    public function show()
    {
        $idCliente = $this->requireAuth();
        $cliente = $this->getClientById($idCliente);

        if (!$cliente) {
            header('Location: index.php?action=logout');
            exit;
        }

        require_once __DIR__ . '/../views/profile/show.php';
    }

    public function edit()
    {
        $idCliente = $this->requireAuth();
        $cliente = $this->getClientById($idCliente);
        $errors = [];

        if (!$cliente) {
            header('Location: index.php?action=logout');
            exit;
        }

        require_once __DIR__ . '/../views/profile/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=profile_edit');
            exit;
        }

        $idCliente = $this->requireAuth();
        $clienteAtual = $this->getClientById($idCliente);

        if (!$clienteAtual) {
            header('Location: index.php?action=logout');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        $errors = [];

        if ($nome === '') {
            $errors[] = 'O nome é obrigatório.';
        }

        if (strlen($nome) > 100) {
            $errors[] = 'O nome deve ter no máximo 100 caracteres.';
        }

        if ($email === '') {
            $errors[] = 'O e-mail é obrigatório.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }

        if ($nome !== '' && $this->existsDuplicate('nome', $nome, $idCliente)) {
            $errors[] = 'Já existe outro usuário com esse nome.';
        }

        if ($email !== '' && $this->existsDuplicate('email', $email, $idCliente)) {
            $errors[] = 'Já existe outro usuário com esse e-mail.';
        }

        $cliente = [
            'id_cliente' => $idCliente,
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone
        ];

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/profile/edit.php';
            return;
        }

        try {
            $sql = "UPDATE cliente
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone
                    WHERE id_cliente = :id_cliente";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;

            if (isset($_SESSION['cliente']) && is_array($_SESSION['cliente'])) {
                $_SESSION['cliente']['nome'] = $nome;
                $_SESSION['cliente']['email'] = $email;
                $_SESSION['cliente']['telefone'] = $telefone;
            }

            header('Location: index.php?action=profile&updated=1');
            exit;

        } catch (PDOException $e) {
            $errors[] = 'Não foi possível atualizar o perfil. Verifique se os dados não estão duplicados.';
            require_once __DIR__ . '/../views/profile/edit.php';
            return;
        }
    }
}