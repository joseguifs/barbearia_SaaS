public function pending()
{
    require_once __DIR__ . '/../models/Scheduling.php';

    $model = new Scheduling($this->pdo);
    $agendamentos = $model->getPending();

    require __DIR__ . '/../views/admin/pending.php';
}