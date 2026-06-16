<?php

class AdminClient
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($busca = '')
    {
        $sql = "
            SELECT id_cliente, nome, telefone, email
            FROM cliente
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($busca)) {
            $sql .= " AND (
                nome LIKE :busca
                OR email LIKE :busca
                OR telefone LIKE :busca
            )";
            $params[':busca'] = '%' . $busca . '%';
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "
            INSERT INTO cliente (nome, telefone, email, senha)
            VALUES (:nome, :telefone, :email, :senha)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':telefone', $data['telefone']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':senha', $data['senha']);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function update($idCliente, $data)
    {
        if (!empty($data['senha'])) {
            $sql = "
                UPDATE cliente
                SET nome = :nome,
                    telefone = :telefone,
                    email = :email,
                    senha = :senha
                WHERE id_cliente = :id_cliente
            ";
        } else {
            $sql = "
                UPDATE cliente
                SET nome = :nome,
                    telefone = :telefone,
                    email = :email
                WHERE id_cliente = :id_cliente
            ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':telefone', $data['telefone']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);

        if (!empty($data['senha'])) {
            $stmt->bindValue(':senha', $data['senha']);
        }

        return $stmt->execute();
    }

    public function delete($idCliente)
    {
        $this->pdo->beginTransaction();

        try {
            // 1. Remove os serviços vinculados aos agendamentos desse cliente
            $sql = "
                DELETE FROM agendamento_servico
                WHERE id_agendamento IN (
                    SELECT id_agendamento
                    FROM agendamento
                    WHERE id_cliente = :id_cliente
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            // 2. Remove os agendamentos desse cliente
            $sql = "
                DELETE FROM agendamento
                WHERE id_cliente = :id_cliente
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            // 3. Remove o cliente
            $sql = "
                DELETE FROM cliente
                WHERE id_cliente = :id_cliente
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}