<?php

class SchedulingReview
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function allPending()
    {
        $sql = "SELECT
                    a.id_agendamento,
                    a.data_hora,
                    a.status,
                    c.nome AS cliente_nome,
                    b.nome AS barbeiro_nome,
                    COALESCE(
                        GROUP_CONCAT(DISTINCT s.nome ORDER BY s.nome SEPARATOR ' - '),
                        'Sem serviços'
                    ) AS servicos
                FROM agendamento a
                INNER JOIN cliente c
                    ON c.id_cliente = a.id_cliente
                INNER JOIN barbeiro b
                    ON b.id_barbeiro = a.id_barbeiro
                LEFT JOIN agendamento_servico ags
                    ON ags.id_agendamento = a.id_agendamento
                LEFT JOIN servico s
                    ON s.id_servico = ags.id_servico
                WHERE a.status = 'pendente'
                GROUP BY
                    a.id_agendamento,
                    a.data_hora,
                    a.status,
                    c.nome,
                    b.nome
                ORDER BY a.data_hora ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function accept($idAgendamento)
    {
        $sql = "UPDATE agendamento
                SET status = 'agendado'
                WHERE id_agendamento = :id_agendamento
                  AND status = 'pendente'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function reject($idAgendamento)
    {
        try {
            $this->pdo->beginTransaction();

            $sqlDelete = "DELETE FROM agendamento_servico
                          WHERE id_agendamento = :id_agendamento";

            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmtDelete->execute();

            $sqlUpdate = "UPDATE agendamento
                          SET status = 'cancelado'
                          WHERE id_agendamento = :id_agendamento
                            AND status = 'pendente'";

            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}