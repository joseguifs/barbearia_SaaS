<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM cliente WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create($nome, $telefone, $email, $senhaHash)
    {
        $sql = "INSERT INTO cliente (nome, telefone, email, senha)
                VALUES (:nome, :telefone, :email, :senha)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':senha', $senhaHash);

        if ($email === null || $email === '') {
            $stmt->bindValue(':email', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':email', $email);
        }

        return $stmt->execute();
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