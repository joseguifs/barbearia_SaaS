<?php
if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('statusClass')) {
    function statusClass($status)
    {
        $status = strtolower((string) $status);

        switch ($status) {
            case 'agendado':
                return 'status-agendado';

            case 'pendente':
                return 'status-pendente';

            case 'cancelado':
                return 'status-cancelado';

            default:
                return 'status-default';
        }
    }
}

if (!function_exists('statusTexto')) {
    function statusTexto($status)
    {
        $status = strtolower((string) $status);

        switch ($status) {
            case 'agendado':
                return 'Agendado';

            case 'pendente':
                return 'Pendente';

            case 'cancelado':
                return 'Cancelado';

            default:
                return $status ? ucfirst($status) : 'Indefinido';
        }
    }
}

if (!function_exists('formatarHorario')) {
    function formatarHorario($dataHora)
    {
        if (empty($dataHora)) {
            return '--:--';
        }

        $timestamp = strtotime($dataHora);

        if (!$timestamp) {
            return '--:--';
        }

        return date('H:i', $timestamp);
    }
}

if (!function_exists('formatarValor')) {
    function formatarValor($valor)
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

$agendamentos = $agendamentos ?? [];
$dadosBarbeiro = $dadosBarbeiro ?? [];

$idBarbeiro = (int) ($_SESSION['id_barbeiro'] ?? ($dadosBarbeiro['id_barbeiro'] ?? 0));
$nomeBarbeiro = $_SESSION['barbeiro_nome'] ?? ($dadosBarbeiro['nome'] ?? 'Barbeiro');

if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
    $primeiraLetra = mb_strtoupper(mb_substr($nomeBarbeiro, 0, 1));
} else {
    $primeiraLetra = strtoupper(substr($nomeBarbeiro, 0, 1));
}

$totalAgendamentos = count($agendamentos);
$totalPendente = 0;
$totalAgendado = 0;
$valorTotalDia = 0;

foreach ($agendamentos as $item) {
    $status = strtolower((string) ($item['status'] ?? ''));

    if ($status === 'pendente') {
        $totalPendente++;
    }

    if ($status === 'agendado') {
        $totalAgendado++;
    }

    $valorTotalDia += (float) ($item['valor_total'] ?? 0);
}

$proximoAgendamento = $agendamentos[0] ?? null;
$hojeFormatado = $hojeFormatado ?? date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Hoje - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/profile-barber.css?v=51"></head>

<body>

<header class="topbar">
    <a class="logo" href="index.php?action=review_pending">BARBERTIME</a>

    <nav class="nav" aria-label="Navegação do barbeiro">
        <a href="index.php?action=review_pending">
            Agendamentos Pendentes
        </a>

        <a href="index.php?action=barber_profile&id=<?= (int) $idBarbeiro ?>" class="active">
            Agendamentos de Hoje
        </a>

        <form
            action="index.php?action=barber_logout"
            method="POST"
            class="logout-form"
            onsubmit="return confirm('Deseja sair da conta de barbeiro?');"
        >
            <button type="submit" class="logout-button">
                Sair
            </button>
        </form>
    </nav>
</header>

<main class="profile-page">

    <section class="hero-profile">
        <div class="hero-copy">
            <span>Painel do barbeiro</span>

            <h1>Olá, <?= e($nomeBarbeiro) ?></h1>

            <p>
                Acompanhe os atendimentos do dia, veja os serviços marcados
                e organize sua rotina de trabalho.
            </p>
        </div>

        <div class="hero-date-card">
            <small>Agenda de hoje</small>
            <strong><?= e($hojeFormatado) ?></strong>
        </div>
    </section>

    <section class="dashboard-grid">
        <article class="profile-card barber-card">
            <div class="avatar" aria-hidden="true">
                <?= e($primeiraLetra) ?>
            </div>

            <div class="profile-info">
                <span class="eyebrow">Barbeiro</span>

                <h2><?= e($nomeBarbeiro) ?></h2>

                <p>
                    Perfil profissional com a agenda diária de atendimentos.
                </p>

                <div class="barber-meta">
                    <div>
                        <span>Status</span>
                        <strong>Agenda ativa</strong>
                    </div>

                    <div>
                        <span>Data</span>
                        <strong><?= e($hojeFormatado) ?></strong>
                    </div>
                </div>
            </div>
        </article>

        <article class="summary-card highlight-card">
            <span>Atendimentos</span>
            <strong><?= e($totalAgendamentos) ?></strong>
            <small>
                <?= $totalAgendamentos === 1 ? 'agendamento para hoje' : 'agendamentos para hoje' ?>
            </small>
        </article>

        <article class="summary-card">
            <span>Confirmados</span>
            <strong><?= e($totalAgendado) ?></strong>
            <small>com status agendado</small>
        </article>

        <article class="summary-card">
            <span>Pendentes</span>
            <strong><?= e($totalPendente) ?></strong>
            <small>aguardando confirmação</small>
        </article>

        <article class="summary-card money-card">
            <span>Total previsto</span>
            <strong><?= e(formatarValor($valorTotalDia)) ?></strong>
            <small>soma dos serviços do dia</small>
        </article>
    </section>

    <section class="agenda-panel">
        <div class="section-header">
            <div>
                <span class="eyebrow">Atendimentos</span>
                <h2>Agenda do dia</h2>
            </div>

            <?php if ($proximoAgendamento): ?>
                <div class="next-badge">
                    Próximo: <?= e(formatarHorario($proximoAgendamento['data_hora'] ?? null)) ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($agendamentos)): ?>

            <div class="empty-state">
                <div class="empty-icon">📅</div>

                <h3>Nenhum agendamento para hoje</h3>

                <p>
                    Quando houver atendimentos marcados para este barbeiro,
                    eles aparecerão nesta área.
                </p>
            </div>

        <?php else: ?>

            <div class="agenda-list">
                <?php foreach ($agendamentos as $agendamento): ?>
                    <?php
                    $telefone = $agendamento['cliente_telefone'] ?? '';
                    $telefoneLimpo = preg_replace('/\D+/', '', $telefone);

                    $servicosTexto = $agendamento['servicos_texto']
                        ?? $agendamento['servicos']
                        ?? 'Serviço não informado';
                    ?>

                    <article class="appointment-card">
                        <div class="time-column">
                            <strong><?= e(formatarHorario($agendamento['data_hora'] ?? null)) ?></strong>
                            <span>Horário</span>
                        </div>

                        <div class="appointment-body">
                            <div class="appointment-header">
                                <div>
                                    <h3><?= e($agendamento['cliente_nome'] ?? 'Cliente não informado') ?></h3>
                                    <p><?= e($servicosTexto) ?></p>
                                </div>

                                <span class="status <?= e(statusClass($agendamento['status'] ?? '')) ?>">
                                    <?= e(statusTexto($agendamento['status'] ?? '')) ?>
                                </span>
                            </div>

                            <div class="appointment-info-grid">
                                <div class="info-box">
                                    <span>Telefone</span>

                                    <?php if (!empty($telefoneLimpo)): ?>
                                        <a href="tel:<?= e($telefoneLimpo) ?>">
                                            <?= e($telefone) ?>
                                        </a>
                                    <?php else: ?>
                                        <strong>Não informado</strong>
                                    <?php endif; ?>
                                </div>

                                <div class="info-box">
                                    <span>Serviços</span>
                                    <strong><?= e($servicosTexto) ?></strong>
                                </div>

                                <div class="info-box price-box">
                                    <span>Valor</span>
                                    <strong><?= e(formatarValor($agendamento['valor_total'] ?? 0)) ?></strong>
                                </div>
                            </div>

                            <?php if (!empty($agendamento['descricao'])): ?>
                                <div class="description-box">
                                    <span>Observação</span>
                                    <p><?= e($agendamento['descricao']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </section>

</main>

</body>
</html>