<?php

class Scheduling
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPending()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id_agendamento,
                c.nome AS cliente,
                b.nome AS barbeiro,
                a.data_hora,
                GROUP_CONCAT(s.nome SEPARATOR ', ') AS servicos
            FROM agendamento a
            JOIN cliente c ON a.id_cliente = c.id_cliente
            JOIN barbeiro b ON a.id_barbeiro = b.id_barbeiro
            LEFT JOIN agendamento_servico ags ON a.id_agendamento = ags.id_agendamento
            LEFT JOIN servico s ON ags.id_servico = s.id_servico
            WHERE a.status = 'pendente'
            GROUP BY a.id_agendamento
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isPending($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT status FROM agendamento WHERE id_agendamento = ?
        ");

        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['status'] === 'pendente';
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("
            UPDATE agendamento SET status = ? WHERE id_agendamento = ?
        ");

        return $stmt->execute([$status, $id]);
    }

    public function deleteServices($id)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM agendamento_servico WHERE id_agendamento = ?
        ");

        return $stmt->execute([$id]);
    }
}