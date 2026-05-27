<?php
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$barbeiroNome = $_SESSION['barbeiro_nome'] ?? 'Barbeiro';

try {
    $dataHora = new DateTime($agendamento['data_hora']);
    $dataFormatada = $dataHora->format('d/m/Y');
    $horaFormatada = $dataHora->format('H:i');
} catch (Exception $e) {
    $dataFormatada = 'Data inválida';
    $horaFormatada = 'Horário inválido';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Agendamento - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../app/css/scheduling-details.css">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=review_pending" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=review_pending">
            Agendamentos Pendentes
        </a>

        <a href="#" class="active">
            Detalhes do Agendamento
        </a>

        <form 
            action="index.php?action=barber_logout" 
            method="POST" 
            class="logout-form"
            onsubmit="return confirm('Deseja sair da conta de barbeiro?');"
        >
            <button type="submit" class="btn-logout">
                Sair
            </button>
        </form>
    </nav>
</header>

<main class="page">
    <section class="details-container">

        <aside class="details-side">
            <span class="side-tag">Detalhes</span>

            <h1>Confira antes de aceitar ou recusar</h1>

            <p>
                Veja as informações completas da solicitação antes de confirmar sua decisão.
            </p>

            <div class="manager-box">
                <div class="manager-avatar">
                    <?= e(mb_strtoupper(mb_substr($barbeiroNome, 0, 1))) ?>
                </div>

                <div>
                    <span>Barbeiro logado</span>
                    <strong><?= e($barbeiroNome) ?></strong>
                </div>
            </div>

            <div class="info-box">
                <span>Status da solicitação</span>
                <strong><?= e($agendamento['status'] ?? 'pendente') ?></strong>
                <p>
                    Analise os dados do cliente, serviços solicitados e horário antes de tomar uma decisão.
                </p>
            </div>
        </aside>

        <section class="details-content">
            <div class="content-header">
                <span>Resumo do atendimento</span>
                <h2>Informações do agendamento</h2>
                <p>
                    Estes dados ajudam o barbeiro a entender melhor a solicitação antes de aceitar ou recusar.
                </p>
            </div>

            <section class="details-card">
                <div class="details-grid">
                    <div class="detail-item">
                        <span>Cliente</span>
                        <strong><?= e($agendamento['cliente_nome'] ?? 'Cliente não informado') ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Status</span>
                        <strong class="status-pill"><?= e($agendamento['status'] ?? 'pendente') ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Data</span>
                        <strong><?= e($dataFormatada) ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Horário</span>
                        <strong><?= e($horaFormatada) ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>E-mail do cliente</span>
                        <strong><?= e($agendamento['cliente_email'] ?? 'Não informado') ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Telefone do cliente</span>
                        <strong><?= e($agendamento['cliente_telefone'] ?? 'Não informado') ?></strong>
                    </div>

                    <div class="detail-item full">
                        <span>Serviços solicitados</span>
                        <strong><?= e($agendamento['servicos'] ?? 'Serviço não informado') ?></strong>
                    </div>

                    <div class="detail-item full">
                        <span>Observações</span>
                        <strong><?= e($agendamento['descricao'] ?? 'Nenhuma observação informada') ?></strong>
                    </div>
                </div>

                <div class="actions">
                    <a href="index.php?action=review_pending" class="btn btn-back">
                        <span class="btn-icon">←</span>
                        <span>Voltar</span>
                    </a>

                    <form action="index.php?action=review_accept" method="POST" class="action-form">
                        <input
                            type="hidden"
                            name="id_agendamento"
                            value="<?= (int) ($agendamento['id_agendamento'] ?? 0) ?>"
                        >

                        <button type="submit" class="btn btn-accept">
                            <span class="btn-icon">✓</span>
                            <span>Aceitar</span>
                        </button>
                    </form>

                    <form action="index.php?action=review_reject" method="POST" class="action-form">
                        <input
                            type="hidden"
                            name="id_agendamento"
                            value="<?= (int) ($agendamento['id_agendamento'] ?? 0) ?>"
                        >

                        <button
                            type="submit"
                            class="btn btn-reject"
                            onclick="return confirm('Deseja realmente recusar este agendamento?');"
                        >
                            <span class="btn-icon">×</span>
                            <span>Recusar</span>
                        </button>
                    </form>
                </div>
            </section>
        </section>

    </section>
</main>

</body>
</html>