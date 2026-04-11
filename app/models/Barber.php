<?php

class Barber
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $sql = "SELECT 
                    b.id_barbeiro,
                    b.nome,
                    COALESCE(
                        GROUP_CONCAT(s.nome ORDER BY s.nome SEPARATOR ' + '),
                        'Sem serviços cadastrados'
                    ) AS especialidades
                FROM barbeiro b
                LEFT JOIN barbeiro_servico bs 
                    ON bs.id_barbeiro = b.id_barbeiro
                LEFT JOIN servico s 
                    ON s.id_servico = bs.id_servico
                GROUP BY b.id_barbeiro, b.nome
                ORDER BY b.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT id_barbeiro, nome
                FROM barbeiro
                WHERE id_barbeiro = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}