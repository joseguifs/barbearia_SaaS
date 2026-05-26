<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

require_once __DIR__ . '/DatabaseTestHelper.php';

abstract class TestCase extends PHPUnitTestCase
{
    protected PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        DatabaseTestHelper::recreateDatabase();
    }

    protected function setUp(): void
    {
        $this->pdo = DatabaseTestHelper::createConnection();
        DatabaseTestHelper::truncateTables($this->pdo);
    }

    protected function createService(string $nome = 'Barba', float $preco = 25.00, int $duracao = 40, string $descricao = 'Serviço de teste'): int
    {
        $sql = "INSERT INTO servico (nome, descricao, preco, duracao)
            VALUES (:nome, :descricao, :preco, :duracao)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':preco', $preco);
        $stmt->bindValue(':duracao', $duracao, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }
    protected function createClient(string $nome = 'Cliente Teste'): int
    {
        $email = strtolower(str_replace(' ', '.', $nome)) . uniqid() . '@teste.com';

        $sql = "INSERT INTO cliente (nome, telefone, email, senha)
                VALUES (:nome, :telefone, :email, :senha)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', '63999990000');
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', password_hash('123456', PASSWORD_DEFAULT));
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    protected function createScheduling(int $idCliente, int $idBarbeiro, string $status = 'agendado'): int
    {
        $sql = "INSERT INTO agendamento (
                    id_cliente,
                    id_barbeiro,
                    data_hora,
                    descricao,
                    status
                ) VALUES (
                    :id_cliente,
                    :id_barbeiro,
                    :data_hora,
                    :descricao,
                    :status
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
        $stmt->bindValue(':data_hora', '2026-12-20 10:00:00');
        $stmt->bindValue(':descricao', 'Agendamento de teste');
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }
}