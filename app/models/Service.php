<?php

class Service
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $sql = "SELECT id_servico, nome, preco, duracao
                FROM servico
                ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByBarber($idBarbeiro)
    {
        $sql = "SELECT 
                    s.id_servico,
                    s.nome,
                    s.preco,
                    s.duracao
                FROM barbeiro_servico bs
                INNER JOIN servico s 
                    ON s.id_servico = bs.id_servico
                WHERE bs.id_barbeiro = :id_barbeiro
                ORDER BY s.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function barberHasAllServices($idBarbeiro, array $servicos)
    {
        if (empty($servicos)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($servicos), '?'));

        $sql = "SELECT COUNT(DISTINCT id_servico) AS total
                FROM barbeiro_servico
                WHERE id_barbeiro = ?
                AND id_servico IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);

        $params = array_merge([$idBarbeiro], $servicos);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'] === count(array_unique($servicos));
    }
}