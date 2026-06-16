<?php

class AdminScheduling
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($filters = [])
    {
        $sql = "
            SELECT
                a.id_agendamento,
                a.id_cliente,
                a.id_barbeiro,
                a.data_hora,
                a.descricao,
                a.status,
                c.nome AS cliente_nome,
                c.email AS cliente_email,
                b.nome AS barbeiro_nome,
                COALESCE(GROUP_CONCAT(DISTINCT s.nome ORDER BY s.nome SEPARATOR ', '), 'Serviço não informado') AS servicos,
                GROUP_CONCAT(DISTINCT s.id_servico ORDER BY s.id_servico SEPARATOR ',') AS servicos_ids
            FROM agendamento a
            INNER JOIN cliente c ON c.id_cliente = a.id_cliente
            INNER JOIN barbeiro b ON b.id_barbeiro = a.id_barbeiro
            LEFT JOIN agendamento_servico ags ON ags.id_agendamento = a.id_agendamento
            LEFT JOIN servico s ON s.id_servico = ags.id_servico
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['data'])) {
            $sql .= " AND DATE(a.data_hora) = :data";
            $params[':data'] = $filters['data'];
        }

        if (!empty($filters['busca'])) {
            $sql .= " AND (
                c.nome LIKE :busca
                OR c.email LIKE :busca
                OR b.nome LIKE :busca
                OR a.descricao LIKE :busca
            )";
            $params[':busca'] = '%' . $filters['busca'] . '%';
        }

        $sql .= "
            GROUP BY
                a.id_agendamento,
                a.id_cliente,
                a.id_barbeiro,
                a.data_hora,
                a.descricao,
                a.status,
                c.nome,
                c.email,
                b.nome
            ORDER BY a.data_hora DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClients()
    {
        $stmt = $this->pdo->query("
            SELECT id_cliente, nome, email
            FROM cliente
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBarbers()
    {
        $stmt = $this->pdo->query("
            SELECT id_barbeiro, nome, email
            FROM barbeiro
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServices()
    {
        $stmt = $this->pdo->query("
            SELECT id_servico, nome, preco, duracao
            FROM servico
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->pdo->beginTransaction();

        try {
            $sql = "
                INSERT INTO agendamento 
                    (id_cliente, id_barbeiro, data_hora, descricao, status)
                VALUES
                    (:id_cliente, :id_barbeiro, :data_hora, :descricao, :status)
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
            $stmt->bindValue(':id_barbeiro', $data['id_barbeiro'], PDO::PARAM_INT);
            $stmt->bindValue(':data_hora', $data['data_hora']);
            $stmt->bindValue(':descricao', $data['descricao']);
            $stmt->bindValue(':status', $data['status']);
            $stmt->execute();

            $idAgendamento = (int) $this->pdo->lastInsertId();

            $this->syncServices($idAgendamento, $data['servicos'] ?? []);

            $this->pdo->commit();

            return $idAgendamento;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update($idAgendamento, $data)
    {
        $this->pdo->beginTransaction();

        try {
            $sql = "
                UPDATE agendamento
                SET 
                    id_cliente = :id_cliente,
                    id_barbeiro = :id_barbeiro,
                    data_hora = :data_hora,
                    descricao = :descricao,
                    status = :status
                WHERE id_agendamento = :id_agendamento
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
            $stmt->bindValue(':id_barbeiro', $data['id_barbeiro'], PDO::PARAM_INT);
            $stmt->bindValue(':data_hora', $data['data_hora']);
            $stmt->bindValue(':descricao', $data['descricao']);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->execute();

            $this->syncServices($idAgendamento, $data['servicos'] ?? []);

            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete($idAgendamento)
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM agendamento_servico
                WHERE id_agendamento = :id_agendamento
            ");
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("
                DELETE FROM agendamento
                WHERE id_agendamento = :id_agendamento
            ");
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function syncServices($idAgendamento, $servicos)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM agendamento_servico
            WHERE id_agendamento = :id_agendamento
        ");
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        if (empty($servicos) || !is_array($servicos)) {
            return;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO agendamento_servico (id_agendamento, id_servico)
            VALUES (:id_agendamento, :id_servico)
        ");

        foreach ($servicos as $idServico) {
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->bindValue(':id_servico', (int) $idServico, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}