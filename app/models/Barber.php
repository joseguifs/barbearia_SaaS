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
                    id_barbeiro,
                    nome,
                    email
                FROM barbeiro
                ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $barbers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($barbers as &$barber) {
            $servicos = $this->getServicesByBarber($barber['id_barbeiro']);

            $barber['servicos'] = $servicos;

            if (!empty($servicos)) {
                $nomesServicos = array_column($servicos, 'nome');
                $barber['especialidades'] = implode(' | ', $nomesServicos);
            } else {
                $barber['especialidades'] = 'Sem serviços cadastrados';
            }
        }

        return $barbers;
    }

    public function find($id)
    {
        $sql = "SELECT 
                    id_barbeiro,
                    nome,
                    email
                FROM barbeiro
                WHERE id_barbeiro = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findWithServices($id)
    {
        $barber = $this->find($id);

        if (!$barber) {
            return false;
        }

        $servicos = $this->getServicesByBarber($id);

        $barber['servicos'] = $servicos;

        if (!empty($servicos)) {
            $nomesServicos = array_column($servicos, 'nome');
            $barber['especialidades'] = implode(' | ', $nomesServicos);
        } else {
            $barber['especialidades'] = 'Sem serviços cadastrados';
        }

        return $barber;
    }

    public function getServicesByBarber($idBarbeiro)
    {
        $sql = "SELECT 
                    s.id_servico,
                    s.nome,
                    s.preco
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

    public function create($nome, $email = null, $servicos = [])
    {
        try {
            $this->pdo->beginTransaction();

            $email = $this->normalizeEmail($email);

            $sql = "INSERT INTO barbeiro (nome, email)
                    VALUES (:nome, :email)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $idBarbeiro = $this->pdo->lastInsertId();

            if (is_array($servicos) && count($servicos) > 0) {
                $this->syncServices($idBarbeiro, $servicos);
            }

            $this->pdo->commit();

            return $idBarbeiro;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function update($id, $nome = null, $email = null, $servicos = null)
    {
        $barber = $this->find($id);

        if (!$barber) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            $fields = [];
            $params = [];

            if ($nome !== null) {
                $fields[] = "nome = :nome";
                $params[':nome'] = $nome;
            }

            if ($email !== null) {
                $fields[] = "email = :email";
                $params[':email'] = $this->normalizeEmail($email);
            }

            if (!empty($fields)) {
                $sql = "UPDATE barbeiro
                        SET " . implode(', ', $fields) . "
                        WHERE id_barbeiro = :id_barbeiro";

                $stmt = $this->pdo->prepare($sql);

                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }

                $stmt->bindValue(':id_barbeiro', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            if (is_array($servicos)) {
                $this->syncServices($id, $servicos);
            }

            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function delete($id)
    {
        $barber = $this->find($id);

        if (!$barber) {
            return [
                'success' => false,
                'reason' => 'not_found',
                'message' => 'Barbeiro não encontrado.'
            ];
        }

        try {
            $this->pdo->beginTransaction();

            $totalAgendamentos = $this->countSchedulingsByBarber($id);
            $proximoBarbeiro = null;
            $totalAgendamentosRedirecionados = 0;

            if ($totalAgendamentos > 0) {
                $proximoBarbeiro = $this->findNextAvailableBarber($id);

                if (!$proximoBarbeiro) {
                    $this->pdo->rollBack();

                    return [
                        'success' => false,
                        'reason' => 'no_replacement_barber',
                        'message' => 'Não foi possível excluir este barbeiro, pois ele possui agendamentos e não existe outro barbeiro disponível para receber esses horários.'
                    ];
                }

                $totalAgendamentosRedirecionados = $this->reassignSchedulingsToNextBarber(
                    $id,
                    $proximoBarbeiro['id_barbeiro']
                );
            }

            $sqlDeleteServices = "DELETE FROM barbeiro_servico
                                  WHERE id_barbeiro = :id_barbeiro";

            $stmtServices = $this->pdo->prepare($sqlDeleteServices);
            $stmtServices->bindValue(':id_barbeiro', $id, PDO::PARAM_INT);
            $stmtServices->execute();

            $sqlDeleteBarber = "DELETE FROM barbeiro
                                WHERE id_barbeiro = :id_barbeiro";

            $stmtBarber = $this->pdo->prepare($sqlDeleteBarber);
            $stmtBarber->bindValue(':id_barbeiro', $id, PDO::PARAM_INT);
            $stmtBarber->execute();

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Barbeiro excluído com sucesso.',
                'deleted_barber' => $barber,
                'reassigned_scheduling_count' => $totalAgendamentosRedirecionados,
                'new_barber' => $proximoBarbeiro
            ];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    private function countSchedulingsByBarber($idBarbeiro)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM agendamento
                WHERE id_barbeiro = :id_barbeiro";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }


    public function findByEmail($email)
    {
        $sql = "SELECT id_barbeiro, nome, email, senha 
                FROM barbeiro 
                WHERE email = :email 
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function findNextAvailableBarber($idBarbeiroAtual)
    {
        $sqlNext = "SELECT id_barbeiro, nome, email
                    FROM barbeiro
                    WHERE id_barbeiro <> :id_barbeiro
                    AND id_barbeiro > :id_barbeiro
                    ORDER BY id_barbeiro ASC
                    LIMIT 1";

        $stmtNext = $this->pdo->prepare($sqlNext);
        $stmtNext->bindValue(':id_barbeiro', $idBarbeiroAtual, PDO::PARAM_INT);
        $stmtNext->execute();

        $nextBarber = $stmtNext->fetch(PDO::FETCH_ASSOC);

        if ($nextBarber) {
            return $nextBarber;
        }

        $sqlFirst = "SELECT id_barbeiro, nome, email
                     FROM barbeiro
                     WHERE id_barbeiro <> :id_barbeiro
                     ORDER BY id_barbeiro ASC
                     LIMIT 1";

        $stmtFirst = $this->pdo->prepare($sqlFirst);
        $stmtFirst->bindValue(':id_barbeiro', $idBarbeiroAtual, PDO::PARAM_INT);
        $stmtFirst->execute();

        return $stmtFirst->fetch(PDO::FETCH_ASSOC);
    }

    private function reassignSchedulingsToNextBarber($idBarbeiroAtual, $idNovoBarbeiro)
    {
        $sql = "UPDATE agendamento
                SET 
                    id_barbeiro = :id_novo_barbeiro,
                    status = 'pendente'
                WHERE id_barbeiro = :id_barbeiro_atual";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_novo_barbeiro', $idNovoBarbeiro, PDO::PARAM_INT);
        $stmt->bindValue(':id_barbeiro_atual', $idBarbeiroAtual, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function syncServices($idBarbeiro, $servicos)
    {
        $servicos = $this->validateServicesExist($servicos);

        $sqlDelete = "DELETE FROM barbeiro_servico
                      WHERE id_barbeiro = :id_barbeiro";

        $stmtDelete = $this->pdo->prepare($sqlDelete);
        $stmtDelete->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
        $stmtDelete->execute();

        if (empty($servicos)) {
            return true;
        }

        $sqlInsert = "INSERT INTO barbeiro_servico (id_barbeiro, id_servico)
                      VALUES (:id_barbeiro, :id_servico)";

        $stmtInsert = $this->pdo->prepare($sqlInsert);

        foreach ($servicos as $idServico) {
            $stmtInsert->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
            $stmtInsert->bindValue(':id_servico', $idServico, PDO::PARAM_INT);
            $stmtInsert->execute();
        }

        return true;
    }

    private function validateServicesExist($servicos)
    {
        $servicos = $this->normalizeServices($servicos);

        if (empty($servicos)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($servicos), '?'));

        $sql = "SELECT id_servico
                FROM servico
                WHERE id_servico IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($servicos as $index => $idServico) {
            $stmt->bindValue($index + 1, $idServico, PDO::PARAM_INT);
        }

        $stmt->execute();

        $servicosExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $servicosExistentes = array_map('intval', $servicosExistentes);

        $servicosInexistentes = array_diff($servicos, $servicosExistentes);

        if (!empty($servicosInexistentes)) {
            $ids = implode(', ', $servicosInexistentes);

            throw new InvalidArgumentException(
                "Os seguintes serviços não existem no banco: {$ids}."
            );
        }

        return $servicos;
    }

    private function normalizeServices($servicos)
    {
        if (!is_array($servicos)) {
            return [];
        }

        $servicos = array_filter($servicos, function ($id) {
            return is_numeric($id) && (int) $id > 0;
        });

        $servicos = array_map('intval', $servicos);

        return array_values(array_unique($servicos));
    }

    private function normalizeEmail($email)
    {
        $email = trim((string) ($email ?? ''));

        if ($email === '') {
            return null;
        }

        $email = strtolower($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('O e-mail do barbeiro é inválido.');
        }

        return $email;
    }
}