<?php

class Scheduling
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
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
                INNER JOIN cliente c ON a.id_cliente = c.id_cliente
                INNER JOIN barbeiro b ON a.id_barbeiro = b.id_barbeiro
                ORDER BY a.data_hora DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getById($id)
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
                INNER JOIN cliente c ON a.id_cliente = c.id_cliente
                INNER JOIN barbeiro b ON a.id_barbeiro = b.id_barbeiro
                WHERE a.id_agendamento = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIdAndClient($idAgendamento, $idCliente)
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
                    ON a.id_cliente = c.id_cliente
                INNER JOIN barbeiro b 
                    ON a.id_barbeiro = b.id_barbeiro
                WHERE a.id_agendamento = :id_agendamento
                AND a.id_cliente = :id_cliente
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);

        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agendamento) {
            return false;
        }

        $servicos = $this->getServicesBySchedulingId($idAgendamento);

        $nomesServicos = [];

        foreach ($servicos as $servico) {
            $nomesServicos[] = $servico['nome'];
        }

        $agendamento['servicos'] = $servicos;

        $agendamento['servicos_texto'] = !empty($nomesServicos)
            ? implode(' + ', $nomesServicos)
            : 'Serviço não informado';

        $agendamento['valor_total'] = $this->getTotalValueBySchedulingId($idAgendamento);

        return $agendamento;
    }


    public function update($id, array $dados)
    {
        $camposPermitidos = [
            'id_cliente',
            'id_barbeiro',
            'data_hora',
            'descricao',
            'status'
        ];

        $campos = [];
        $params = [
            ':id' => $id
        ];

        foreach ($camposPermitidos as $campo) {
            if (array_key_exists($campo, $dados)) {
                $campos[] = "$campo = :$campo";
                $params[":$campo"] = $dados[$campo];
            }
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE agendamento 
                SET " . implode(', ', $campos) . "
                WHERE id_agendamento = :id";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $valor) {
            $stmt->bindValue($param, $valor);
        }

        return $stmt->execute();
    }

    public function create($dados, array $servicos)
    {
        try {
            $iniciouTransacao = false;

            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $iniciouTransacao = true;
            }

            $sql = "INSERT INTO agendamento
                    (id_cliente, id_barbeiro, data_hora, descricao, status)
                    VALUES
                    (:id_cliente, :id_barbeiro, :data_hora, :descricao, :status)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_cliente', $dados['id_cliente'], PDO::PARAM_INT);
            $stmt->bindValue(':id_barbeiro', $dados['id_barbeiro'], PDO::PARAM_INT);
            $stmt->bindValue(':data_hora', $dados['data_hora']);
            $stmt->bindValue(':descricao', $dados['descricao'] ?? null);
            $stmt->bindValue(':status', $dados['status'] ?? 'pendente');
            $stmt->execute();

            $idAgendamento = (int)$this->pdo->lastInsertId();

            if (!empty($servicos)) {
                $sqlServico = "INSERT INTO agendamento_servico
                               (id_agendamento, id_servico)
                               VALUES
                               (:id_agendamento, :id_servico)";

                $stmtServico = $this->pdo->prepare($sqlServico);

                foreach ($servicos as $idServico) {
                    $idServico = (int)$idServico;

                    if ($idServico <= 0) {
                        continue;
                    }

                    $stmtServico->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
                    $stmtServico->bindValue(':id_servico', $idServico, PDO::PARAM_INT);
                    $stmtServico->execute();
                }
            }

            if ($iniciouTransacao) {
                $this->pdo->commit();
            }

            return $idAgendamento;
        } catch (Exception $e) {
            if (isset($iniciouTransacao) && $iniciouTransacao && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

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

    public function getActiveByClient($idCliente)
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
                    ON a.id_cliente = c.id_cliente
                INNER JOIN barbeiro b 
                    ON a.id_barbeiro = b.id_barbeiro
                WHERE a.id_cliente = :id_cliente
                AND a.status IN ('pendente', 'agendado')
                AND a.data_hora >= NOW()
                ORDER BY a.data_hora ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();

        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($agendamentos as &$agendamento) {
            $idAgendamento = $agendamento['id_agendamento'];

            $servicos = $this->getServicesBySchedulingId($idAgendamento);

            $nomesServicos = [];

            foreach ($servicos as $servico) {
                $nomesServicos[] = $servico['nome'];
            }

            $agendamento['servicos'] = $servicos;

            $agendamento['servicos_texto'] = !empty($nomesServicos)
                ? implode(' + ', $nomesServicos)
                : 'Serviço não informado';

            $agendamento['valor_total'] = $this->getTotalValueBySchedulingId($idAgendamento);
        }

        return $agendamentos;
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

    public function getNextByClient($idCliente)
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
                  AND a.data_hora >= NOW()
                  AND a.status IN ('pendente', 'agendado')
                ORDER BY a.data_hora ASC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agendamento) {
            return null;
        }

        $servicos = $this->getServicesBySchedulingId($agendamento['id_agendamento']);
        $nomesServicos = [];

        foreach ($servicos as $servico) {
            $nomesServicos[] = $servico['nome'];
        }

        $agendamento['servicos'] = $servicos;

        $agendamento['servicos_texto'] = !empty($nomesServicos)
            ? implode(' + ', $nomesServicos)
            : 'Serviço não informado';

        $agendamento['valor_total'] = $this->getTotalValueBySchedulingId($agendamento['id_agendamento']);

        return $agendamento;
    }

    public function updateDateTime($idAgendamento, $dataHora)
    {
        $sql = "UPDATE agendamento
                SET data_hora = :data_hora
                WHERE id_agendamento = :id_agendamento";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':data_hora', $dataHora);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getDailyAgenda($idBarbeiro, $data)
    {
        $sql = "SELECT 
                    a.id_agendamento,
                    a.data_hora,
                    a.status,
                    a.descricao,
                    c.nome AS cliente_nome,
                    c.telefone AS cliente_telefone
                FROM agendamento a
                INNER JOIN cliente c ON a.id_cliente = c.id_cliente
                WHERE a.id_barbeiro = :id_barbeiro
                  AND DATE(a.data_hora) = :data
                  AND a.status IN ('pendente', 'agendado')
                ORDER BY a.data_hora ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
        $stmt->bindValue(':data', $data);
        $stmt->execute();

        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($agendamentos as &$agendamento) {
            $servicos = $this->getServicesBySchedulingId($agendamento['id_agendamento']);
            $nomesServicos = array_column($servicos, 'nome');

            $agendamento['servicos'] = $servicos;
            $agendamento['servicos_texto'] = !empty($nomesServicos)
                ? implode(' + ', $nomesServicos)
                : 'Serviço não informado';

            $agendamento['valor_total'] = $this->getTotalValueBySchedulingId($agendamento['id_agendamento']);
        }

        return $agendamentos;
    }

    public function hasScheduleConflict($idBarbeiro, $dataHora, $idAgendamentoIgnorar = null, array $servicos = [])
    {
        $dataTimestamp = strtotime($dataHora);

        if ($dataTimestamp === false) {
            return true;
        }

        $data = date('Y-m-d', $dataTimestamp);

        $duracaoNovoAgendamento = !empty($servicos)
            ? $this->getTotalDurationByServices($servicos)
            : 30;

        if ($duracaoNovoAgendamento <= 0) {
            $duracaoNovoAgendamento = 30;
        }

        $inicioNovo = new DateTime($dataHora);
        $fimNovo = clone $inicioNovo;
        $fimNovo->modify("+{$duracaoNovoAgendamento} minutes");

        $agendamentosExistentes = $this->getAppointmentsByBarberAndDate(
            $idBarbeiro,
            $data,
            $idAgendamentoIgnorar
        );

        foreach ($agendamentosExistentes as $agendamento) {
            $inicioExistente = new DateTime($agendamento['data_hora']);
            $duracaoExistente = (int)($agendamento['duracao_total'] ?? 30);

            if ($duracaoExistente <= 0) {
                $duracaoExistente = 30;
            }

            $fimExistente = clone $inicioExistente;
            $fimExistente->modify("+{$duracaoExistente} minutes");

            $sobrepoe = $inicioNovo < $fimExistente && $fimNovo > $inicioExistente;

            if ($sobrepoe) {
                return true;
            }
        }

        return false;
    }


    public function deleteByIdAndClient($idAgendamento, $idCliente)
    {
        try {
            $iniciouTransacao = false;

            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $iniciouTransacao = true;
            }

            $sqlDeleteServicos = "DELETE ags
                                  FROM agendamento_servico ags
                                  INNER JOIN agendamento a
                                      ON a.id_agendamento = ags.id_agendamento
                                  WHERE ags.id_agendamento = :id_agendamento
                                  AND a.id_cliente = :id_cliente";

            $stmtServicos = $this->pdo->prepare($sqlDeleteServicos);
            $stmtServicos->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmtServicos->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmtServicos->execute();

            $sql = "DELETE FROM agendamento
                    WHERE id_agendamento = :id_agendamento
                    AND id_cliente = :id_cliente";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            $apagou = $stmt->rowCount() > 0;

            if ($iniciouTransacao) {
                $this->pdo->commit();
            }

            return $apagou;
        } catch (Exception $e) {
            if (isset($iniciouTransacao) && $iniciouTransacao && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }


    public function deleteById($idAgendamento)
    {
        try {
            $iniciouTransacao = false;

            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $iniciouTransacao = true;
            }

            $sqlServicos = "DELETE FROM agendamento_servico
                            WHERE id_agendamento = :id_agendamento";

            $stmtServicos = $this->pdo->prepare($sqlServicos);
            $stmtServicos->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmtServicos->execute();

            $sql = "DELETE FROM agendamento
                    WHERE id_agendamento = :id_agendamento";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmt->execute();

            $apagou = $stmt->rowCount() > 0;

            if ($iniciouTransacao) {
                $this->pdo->commit();
            }

            return $apagou;
        } catch (Exception $e) {
            if (isset($iniciouTransacao) && $iniciouTransacao && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function cancelByIdAndClient($idAgendamento, $idCliente)
    {
        $sql = "UPDATE agendamento
                SET status = 'cancelado'
                WHERE id_agendamento = :id_agendamento
                AND id_cliente = :id_cliente
                AND status IN ('pendente', 'agendado')";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function updateServicesBySchedulingId($idAgendamento, array $servicos)
    {
        try {
            $iniciouTransacao = false;

            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $iniciouTransacao = true;
            }

            $sqlDelete = "DELETE FROM agendamento_servico
                          WHERE id_agendamento = :id_agendamento";

            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
            $stmtDelete->execute();

            $servicos = array_map('intval', $servicos);
            $servicos = array_values(array_filter($servicos));

            if (!empty($servicos)) {
                $sqlInsert = "INSERT INTO agendamento_servico 
                              (id_agendamento, id_servico)
                              VALUES 
                              (:id_agendamento, :id_servico)";

                $stmtInsert = $this->pdo->prepare($sqlInsert);

                foreach ($servicos as $idServico) {
                    $stmtInsert->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
                    $stmtInsert->bindValue(':id_servico', $idServico, PDO::PARAM_INT);
                    $stmtInsert->execute();
                }
            }

            if ($iniciouTransacao) {
                $this->pdo->commit();
            }

            return true;
        } catch (Exception $e) {
            if (isset($iniciouTransacao) && $iniciouTransacao && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }




    public function updateScheduleChange($idAgendamento, array $dados, $servicos = null)
    {
        try {
            $this->pdo->beginTransaction();

            if (!empty($dados)) {
                $this->update($idAgendamento, $dados);
            }

            if ($servicos !== null) {
                $this->updateServicesBySchedulingId($idAgendamento, $servicos);
            }

            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    public function getTotalDurationByServices(array $servicos)
    {
        if (empty($servicos)) {
            return 0;
        }

        $servicos = array_map('intval', $servicos);
        $servicos = array_values(array_filter($servicos));

        if (empty($servicos)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($servicos), '?'));

        $sql = "SELECT COALESCE(SUM(duracao), 0) AS duracao_total
                FROM servico
                WHERE id_servico IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($servicos);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($resultado['duracao_total'] ?? 0);
    }



    public function getAppointmentsByBarberAndDate(
    $idBarbeiro,
    $data,
    $idAgendamentoIgnorar = null
) {
    $sql = "SELECT 
                a.id_agendamento,
                a.data_hora,
                a.status,
                a.descricao,
                c.nome AS cliente_nome,
                c.telefone AS cliente_telefone,
                COALESCE(SUM(s.duracao), 30) AS duracao_total
            FROM agendamento a
            INNER JOIN cliente c 
                ON a.id_cliente = c.id_cliente
            LEFT JOIN agendamento_servico ags
                ON a.id_agendamento = ags.id_agendamento
            LEFT JOIN servico s
                ON ags.id_servico = s.id_servico
            WHERE a.id_barbeiro = :id_barbeiro
              AND DATE(a.data_hora) = :data
              AND a.status IN ('pendente', 'agendado')";

    if ($idAgendamentoIgnorar !== null) {
        $sql .= " AND a.id_agendamento != :id_agendamento";
    }

    $sql .= " 
            GROUP BY 
                a.id_agendamento,
                a.data_hora,
                a.status,
                a.descricao,
                c.nome,
                c.telefone
            ORDER BY a.data_hora ASC";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
    $stmt->bindValue(':data', $data);

    if ($idAgendamentoIgnorar !== null) {
        $stmt->bindValue(':id_agendamento', $idAgendamentoIgnorar, PDO::PARAM_INT);
    }

    $stmt->execute();

    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($agendamentos as &$agendamento) {
        $servicos = $this->getServicesBySchedulingId(
            $agendamento['id_agendamento']
        );

        $nomesServicos = array_column($servicos, 'nome');

        $agendamento['servicos'] = $servicos;

        $agendamento['servicos_texto'] = !empty($nomesServicos)
            ? implode(' + ', $nomesServicos)
            : 'Serviço não informado';

        $agendamento['duracao_total'] = (int)$agendamento['duracao_total'];

        if ($agendamento['duracao_total'] <= 0) {
            $agendamento['duracao_total'] = 30;
        }
    }

    return $agendamentos;
}



    public function getAvailableTimes(
    $idBarbeiro,
    $data,
    array $servicos,
    $horaInicial = '08:00',
    $horaFinal = '17:00',
    $intervaloMinutos = 30,
    $idAgendamentoIgnorar = null
    ) {
        $duracaoNovoAgendamento = $this->getTotalDurationByServices($servicos);

        if ($duracaoNovoAgendamento <= 0) {
            $duracaoNovoAgendamento = 30;
        }

        $inicioExpediente = new DateTime($data . ' ' . $horaInicial . ':00');
        $fimExpediente = new DateTime($data . ' ' . $horaFinal . ':00');

        $agendamentosExistentes = $this->getAppointmentsByBarberAndDate(
            $idBarbeiro,
            $data,
            $idAgendamentoIgnorar
        );

        $horariosDisponiveis = [];

        for (
            $horario = clone $inicioExpediente;
            $horario < $fimExpediente;
            $horario->modify("+{$intervaloMinutos} minutes")
        ) {
            $inicioNovo = clone $horario;

            $fimNovo = clone $inicioNovo;
            $fimNovo->modify("+{$duracaoNovoAgendamento} minutes");

            if ($fimNovo > $fimExpediente) {
                continue;
            }

            $temConflito = false;

            foreach ($agendamentosExistentes as $agendamento) {
                $inicioExistente = new DateTime($agendamento['data_hora']);

                $duracaoExistente = (int)($agendamento['duracao_total'] ?? 30);

                if ($duracaoExistente <= 0) {
                    $duracaoExistente = 30;
                }

                $fimExistente = clone $inicioExistente;
                $fimExistente->modify("+{$duracaoExistente} minutes");

                /*
                    Existe conflito quando o novo agendamento começa antes
                    do existente terminar e termina depois do existente começar.
                */
                $sobrepoe = $inicioNovo < $fimExistente && $fimNovo > $inicioExistente;

                if ($sobrepoe) {
                    $temConflito = true;
                    break;
                }
            }

            if (!$temConflito) {
                $horariosDisponiveis[] = $inicioNovo->format('H:i');
            }
        }

        return $horariosDisponiveis;
    }

    public function isTimeAvailable(
    $idBarbeiro,
    $dataHora,
    array $servicos,
    $idAgendamentoIgnorar = null
    ) {
        $data = date('Y-m-d', strtotime($dataHora));
        $hora = date('H:i', strtotime($dataHora));

        $horariosDisponiveis = $this->getAvailableTimes(
            $idBarbeiro,
            $data,
            $servicos,
            '08:00',
            '17:00',
            30,
            $idAgendamentoIgnorar
        );

        return in_array($hora, $horariosDisponiveis, true);
    }

}
