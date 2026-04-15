<?php

class Scheduling
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($dados, array $servicos)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO agendamento
                    (id_cliente, id_barbeiro, data_hora, descricao, status)
                    VALUES
                    (:id_cliente, :id_barbeiro, :data_hora, :descricao, :status)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $dados['id_cliente'], PDO::PARAM_INT);
            $stmt->bindValue(':id_barbeiro', $dados['id_barbeiro'], PDO::PARAM_INT);
            $stmt->bindValue(':data_hora', $dados['data_hora']);
            $stmt->bindValue(':descricao', $dados['descricao']);
            $stmt->bindValue(':status', $dados['status']);
            $stmt->execute();

            $idAgendamento = $this->pdo->lastInsertId();

            $sqlServico = "INSERT INTO agendamento_servico
                           (id_agendamento, id_servico)
                           VALUES
                           (:id_agendamento, :id_servico)";

            $stmtServico = $this->pdo->prepare($sqlServico);

            foreach ($servicos as $idServico) {
                $stmtServico->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
                $stmtServico->bindValue(':id_servico', $idServico, PDO::PARAM_INT);
                $stmtServico->execute();
            }

            $this->pdo->commit();
            return $idAgendamento;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function find($idAgendamento)
    {
        $sql = "SELECT
                    a.id_agendamento,
                    a.id_cliente,
                    a.id_barbeiro,
                    a.data_hora,
                    a.descricao,
                    a.status,
                    c.nome AS cliente_nome,
                    b.nome AS barbeiro_nome
                FROM agendamento a
                INNER JOIN cliente c
                    ON c.id_cliente = a.id_cliente
                INNER JOIN barbeiro b
                    ON b.id_barbeiro = a.id_barbeiro
                WHERE a.id_agendamento = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getServicesBySchedulingId($idAgendamento)
    {
        $sql = "SELECT
                    s.id_servico,
                    s.nome,
                    s.preco,
                    s.duracao
                FROM agendamento_servico ags
                INNER JOIN servico s
                    ON s.id_servico = ags.id_servico
                WHERE ags.id_agendamento = :id_agendamento
                ORDER BY s.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalValueBySchedulingId($idAgendamento)
    {
        $sql = "SELECT COALESCE(SUM(s.preco), 0) AS total
                FROM agendamento_servico ags
                INNER JOIN servico s
                    ON s.id_servico = ags.id_servico
                WHERE ags.id_agendamento = :id_agendamento";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (float)$result['total'] : 0;
    }

    public function allByClient($idCliente)
    {
        $sql = "SELECT
                    a.id_agendamento,
                    a.data_hora,
                    a.status,
                    b.nome AS barbeiro_nome
                FROM agendamento a
                INNER JOIN barbeiro b
                    ON b.id_barbeiro = a.id_barbeiro
                WHERE a.id_cliente = :id_cliente
                ORDER BY a.data_hora DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}