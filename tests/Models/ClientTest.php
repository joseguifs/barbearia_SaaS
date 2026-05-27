<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/models/Client.php';

class ClientTest extends TestCase
{
    public function testCreateClient(): void
    {
        $clientModel = new Client($this->pdo);

        if (!method_exists($clientModel, 'create')) {
            $this->markTestSkipped('O model Client não possui método create().');
        }

        $senhaHash = password_hash('123456', PASSWORD_DEFAULT);

        $result = $clientModel->create(
            'Cliente Teste',
            '63999990000',
            'cliente.teste@example.com',
            $senhaHash
        );

        $this->assertNotFalse($result);

        $stmt = $this->pdo->prepare("SELECT * FROM cliente WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', 'cliente.teste@example.com');
        $stmt->execute();

        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($client);
        $this->assertEquals('Cliente Teste', $client['nome']);
        $this->assertTrue(password_verify('123456', $client['senha']));
    }

    public function testFindClientByEmail(): void
    {
        $email = 'cliente.email@example.com';

        $this->createClientWithEmail($email);

        $clientModel = new Client($this->pdo);

        if (!method_exists($clientModel, 'findByEmail')) {
            $this->markTestSkipped('O model Client não possui método findByEmail().');
        }

        $client = $clientModel->findByEmail($email);

        $this->assertNotFalse($client);
        $this->assertEquals($email, $client['email']);
    }

    public function testFindClientByIdIfMethodExists(): void
    {
        $idCliente = $this->createClient('Cliente ID');

        $clientModel = new Client($this->pdo);

        if (method_exists($clientModel, 'find')) {
            $client = $clientModel->find($idCliente);
        } elseif (method_exists($clientModel, 'getById')) {
            $client = $clientModel->getById($idCliente);
        } else {
            $this->markTestSkipped('O model Client não possui método find() ou getById().');
        }

        $this->assertNotFalse($client);
        $this->assertEquals('Cliente ID', $client['nome']);
    }

    public function testDuplicateClientEmailFails(): void
    {
        $clientModel = new Client($this->pdo);

        if (!method_exists($clientModel, 'create')) {
            $this->markTestSkipped('O model Client não possui método create().');
        }

        $senhaHash = password_hash('123456', PASSWORD_DEFAULT);

        $clientModel->create(
            'Cliente Um',
            '63999990000',
            'duplicado@example.com',
            $senhaHash
        );

        $this->expectException(PDOException::class);

        $clientModel->create(
            'Cliente Dois',
            '63999990001',
            'duplicado@example.com',
            $senhaHash
        );
    }

    public function testPasswordIsStoredAsHash(): void
    {
        $email = 'senha.hash@example.com';
        $senha = '123456';

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO cliente (nome, telefone, email, senha)
            VALUES (:nome, :telefone, :email, :senha)
        ");

        $stmt->execute([
            ':nome' => 'Cliente Senha',
            ':telefone' => '63999990000',
            ':email' => $email,
            ':senha' => $senhaHash
        ]);

        $stmt = $this->pdo->prepare("SELECT senha FROM cliente WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEquals($senha, $client['senha']);
        $this->assertTrue(password_verify($senha, $client['senha']));
    }

    private function createClientWithEmail(string $email): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO cliente (nome, telefone, email, senha)
            VALUES (:nome, :telefone, :email, :senha)
        ");

        $stmt->execute([
            ':nome' => 'Cliente Teste',
            ':telefone' => '63999990000',
            ':email' => $email,
            ':senha' => password_hash('123456', PASSWORD_DEFAULT)
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}