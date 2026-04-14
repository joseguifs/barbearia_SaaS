public function getPending()
{
    $stmt = $this->pdo->prepare("
        SELECT * FROM agendamento
        WHERE status = 'pendente'
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}