<?php

class Client
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $sql = "SELECT id_cliente, nome, email, telefone
                FROM cliente
                ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT id_cliente, nome, email, telefone
                FROM cliente
                WHERE id_cliente = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT *
                FROM cliente
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nome, $telefone, $email, $senha) 
    {
        $sql = "INSERT INTO cliente (nome, telefone, email, senha) VALUES (:nome, :telefone, :email, :senha)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':email' => $email,
            ':senha' => $senha
        ]);

        return [
            'id_cliente' => $this->pdo->lastInsertId(),
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email
        ];
        
    }

    public function update($id, $nome, $telefone, $email) 
    {
        $sql = "UPDATE cliente SET nome = :nome, telefone = :telefone, email = :email WHERE id_cliente = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':email' => $email,
            ':id' => $id
        ]);

        return [
            'id_cliente' => $this->pdo->lastInsertId(),
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email
        ];
    }

    public function delete($id) 
    {
        $sql = "DELETE FROM cliente WHERE id_cliente = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function updatePassword($idCliente, $senhaHash)
    {
        $sql = "UPDATE cliente
        SET senha = :senha
        WHERE id_cliente = :id_cliente";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':senha', $senhaHash);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);

        return $stmt->execute();
    }

}

