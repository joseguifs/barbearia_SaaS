<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarDataHoraHistorico')) {
    function formatarDataHoraHistorico($dataHora)
    {
        if (empty($dataHora)) {
            return '-';
        }

        $timestamp = strtotime($dataHora);

        if (!$timestamp) {
            return $dataHora;
        }

        return date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('formatarValorHistorico')) {
    function formatarValorHistorico($valor)
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (!function_exists('statusTextoHistorico')) {
    function statusTextoHistorico($status, $dataHora)
    {
        $status = strtolower((string) $status);
        $timestamp = strtotime($dataHora);

        if ($status === 'agendado' && $timestamp && $timestamp < time()) {
            return 'Finalizado';
        }

        if ($status === 'pendente' && $timestamp && $timestamp < time()) {
            return 'Expirado';
        }

        switch ($status) {
            case 'pendente':
                return 'Pendente';

            case 'agendado':
                return 'Agendado';

            case 'cancelado':
                return 'Cancelado';

            case 'concluido':
                return 'Concluído';

            default:
                return $status ? ucfirst($status) : 'Indefinido';
        }
    }
}

if (!function_exists('statusClassHistorico')) {
    function statusClassHistorico($status, $dataHora)
    {
        $status = strtolower((string) $status);
        $timestamp = strtotime($dataHora);

        if ($status === 'agendado' && $timestamp && $timestamp < time()) {
            return 'status-concluido';
        }

        if ($status === 'pendente' && $timestamp && $timestamp < time()) {
            return 'status-expirado';
        }

        switch ($status) {
            case 'pendente':
                return 'status-pendente';

            case 'agendado':
                return 'status-agendado';

            case 'cancelado':
                return 'status-cancelado';

            case 'concluido':
                return 'status-concluido';

            default:
                return 'status-default';
        }
    }
}

$clienteNomeSessao = $_SESSION['cliente_nome'] ?? $_SESSION['nome'] ?? 'Cliente';

$clienteInicial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($clienteNomeSessao, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($clienteNomeSessao, 0, 1));

$historico = $historico ?? [];
$totalHistorico = count($historico);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Agendamentos - BarberTime</title>

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css?v=3">

    <style>
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .history-card {
            padding: 22px;
            border-radius: 22px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
            transition: 0.25s ease;
        }

        .history-card:hover {
            border-color: rgba(197, 157, 95, 0.35);
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
        }

        .history-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .history-title h3 {
            color: #fff;
            font-size: 1.15rem;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .history-title p {
            color: var(--text-muted);
            line-height: 1.5;
        }

        .history-info {
            display: grid;
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .info-item {
            padding: 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.045);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .info-item span {
            display: block;
            color: var(--primary);
            font-size: 0.76rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }

        .info-item strong {
            color: #fff;
            font-size: 0.95rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-pendente {
            color: #ffd27d;
            background: rgba(255, 193, 7, 0.12);
            border: 1px solid rgba(255, 193, 7, 0.32);
        }

        .status-agendado {
            color: #b9f6ca;
            background: rgba(76, 175, 80, 0.14);
            border: 1px solid rgba(76, 175, 80, 0.35);
        }

        .status-cancelado {
            color: #ffcdd2;
            background: rgba(244, 67, 54, 0.14);
            border: 1px solid rgba(244, 67, 54, 0.35);
        }

        .status-concluido {
            color: #bbdefb;
            background: rgba(33, 150, 243, 0.14);
            border: 1px solid rgba(33, 150, 243, 0.35);
        }

        .status-expirado {
            color: #d7ccc8;
            background: rgba(141, 110, 99, 0.14);
            border: 1px solid rgba(141, 110, 99, 0.35);
        }

        .status-default {
            color: rgba(255, 255, 255, 0.72);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .history-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-view,
        .btn-new {
            min-height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            transition: 0.25s ease;
        }

        .btn-view {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-new {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .btn-view:hover,
        .btn-new:hover {
            transform: translateY(-2px);
            opacity: 0.96;
        }

        .empty-state {
            padding: 30px;
            border-radius: 22px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
            text-align: center;
        }

        .empty-state h3 {
            color: #fff;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .reveal-item {
            opacity: 0;
            transform: translateY(26px);
            transition:
                opacity 0.7s ease,
                transform 0.7s ease;
        }

        .reveal-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .micro-lift {
            transition:
                transform 0.28s ease,
                box-shadow 0.28s ease,
                border-color 0.28s ease,
                background 0.28s ease;
        }

        .micro-lift:hover {
            transform: translateY(-6px);
        }

        @media (max-width: 768px) {
            .history-top {
                flex-direction: column;
            }

            .history-info {
                grid-template-columns: 1fr;
            }

            .history-actions {
                flex-direction: column;
            }

            .btn-view,
            .btn-new {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<header class="topbar reveal-item">
    <a href="index.php?action=home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=home">Início</a>
        <a href="index.php?action=scheduling_create">Agendar</a>
        <a href="index.php?action=scheduling_list">Meus Agendamentos</a>
        <a href="index.php?action=scheduling_history" class="active">Histórico</a>
        <a href="index.php?action=logout" class="btn-logout">Sair</a>
    </nav>
</header>

<main class="page">

    <section class="schedule-container">

        <aside class="schedule-side reveal-item">
            <span class="side-tag">Histórico</span>

            <h1>Histórico de agendamentos</h1>

            <p>
                Consulte seus atendimentos anteriores, cancelados ou expirados.
                Seus agendamentos ativos continuam na tela de Meus Agendamentos.
            </p>

            <div class="client-box micro-lift">
                <div class="client-avatar">
                    <?= e($clienteInicial) ?>
                </div>

                <div>
                    <span>Cliente logado</span>
                    <strong><?= e($clienteNomeSessao) ?></strong>
                </div>
            </div>

            <div class="info-box micro-lift">
                <strong>Total no histórico</strong>

                <p>
                    <?= $totalHistorico === 1
                        ? 'Existe 1 agendamento no seu histórico.'
                        : 'Existem ' . $totalHistorico . ' agendamentos no seu histórico.'
                    ?>
                </p>
            </div>
        </aside>

        <section class="schedule-content reveal-item">
            <div class="content-header">
                <span>BarberTime</span>

                <h2>Meus históricos</h2>

                <p>
                    Veja os registros de agendamentos que já passaram, foram cancelados
                    ou não estão mais ativos.
                </p>
            </div>

            <div class="schedule-form">

                <?php if (empty($historico)): ?>

                    <section class="empty-state micro-lift reveal-item">
                        <h3>Nenhum histórico encontrado</h3>

                        <p>
                            Quando você tiver agendamentos antigos, cancelados ou expirados,
                            eles aparecerão nesta área.
                        </p>

                        <a href="index.php?action=scheduling_create" class="btn-new">
                            Criar agendamento
                        </a>
                    </section>

                <?php else: ?>

                    <section class="history-list">
                        <?php foreach ($historico as $agendamento): ?>
                            <?php
                                $statusClasse = statusClassHistorico(
                                    $agendamento['status'] ?? '',
                                    $agendamento['data_hora'] ?? ''
                                );

                                $statusTexto = statusTextoHistorico(
                                    $agendamento['status'] ?? '',
                                    $agendamento['data_hora'] ?? ''
                                );
                            ?>

                            <article class="history-card micro-lift reveal-item">
                                <div class="history-top">
                                    <div class="history-title">
                                        <h3><?= e($agendamento['servicos_texto'] ?? 'Serviço não informado') ?></h3>

                                        <p>
                                            Atendimento com <?= e($agendamento['barbeiro_nome'] ?? 'barbeiro não informado') ?>.
                                        </p>
                                    </div>

                                    <span class="status-badge <?= e($statusClasse) ?>">
                                        <?= e($statusTexto) ?>
                                    </span>
                                </div>

                                <div class="history-info">
                                    <div class="info-item">
                                        <span>Data e horário</span>
                                        <strong><?= e(formatarDataHoraHistorico($agendamento['data_hora'] ?? null)) ?></strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Valor</span>
                                        <strong><?= e(formatarValorHistorico($agendamento['valor_total'] ?? 0)) ?></strong>
                                    </div>

                                    <div class="info-item">
                                        <span>Barbeiro</span>
                                        <strong><?= e($agendamento['barbeiro_nome'] ?? 'Não informado') ?></strong>
                                    </div>
                                </div>

                                <?php if (!empty($agendamento['descricao'])): ?>
                                    <div class="info-item" style="margin-top: 14px;">
                                        <span>Observação</span>
                                        <strong><?= e($agendamento['descricao']) ?></strong>
                                    </div>
                                <?php endif; ?>

                                <div class="history-actions">
                                    <a
                                        href="index.php?action=scheduling_get&id=<?= (int) $agendamento['id_agendamento'] ?>"
                                        class="btn-view"
                                    >
                                        Ver detalhes
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>

                <?php endif; ?>

            </div>
        </section>

    </section>

</main>

<script>
    function ativarReveals(container = document) {
        const itens = container.querySelectorAll('.reveal-item:not(.is-visible)');

        if (!('IntersectionObserver' in window)) {
            itens.forEach(function (item) {
                item.classList.add('is-visible');
            });

            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12
        });

        itens.forEach(function (item, index) {
            item.style.transitionDelay = `${index * 0.05}s`;
            observer.observe(item);
        });
    }

    ativarReveals();
</script>

</body>
</html>