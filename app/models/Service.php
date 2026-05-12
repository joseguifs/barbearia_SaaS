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
        $sql = "SELECT id_servico, nome, descricao, preco, duracao
                FROM servico
                ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT id_servico, nome, descricao, preco, duracao
                FROM servico
                WHERE id_servico = :id_servico
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_servico', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $dados)
    {
        $sql = "INSERT INTO servico (nome, descricao, preco, duracao)
                VALUES (:nome, :descricao, :preco, :duracao)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':preco', $dados['preco']);
        $stmt->bindValue(':duracao', $dados['duracao'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function update($id, array $dados)
    {
        $sql = "UPDATE servico
                SET nome = :nome,
                    descricao = :descricao,
                    preco = :preco,
                    duracao = :duracao
                WHERE id_servico = :id_servico";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':preco', $dados['preco']);
        $stmt->bindValue(':duracao', $dados['duracao'], PDO::PARAM_INT);
        $stmt->bindValue(':id_servico', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM servico WHERE id_servico = :id_servico";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_servico', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function allByBarber($idBarbeiro)
    {
        $sql = "SELECT 
                    s.id_servico,
                    s.nome,
                    s.descricao,
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
