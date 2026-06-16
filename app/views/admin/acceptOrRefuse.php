<?php

$message = $message ?? $_GET['message'] ?? null;
$agendamentos = $agendamentos ?? [];
$totalPendentes = count($agendamentos);

$barbeiroNome = $_SESSION['barbeiro_nome'] ?? 'Barbeiro';
$idBarbeiro = (int) ($_SESSION['id_barbeiro'] ?? 0);

$inicialBarbeiro = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($barbeiroNome, 0, 1))
    : strtoupper(substr($barbeiroNome, 0, 1));

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos Pendentes - BarberTime</title>
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling-review.css">
</head>

<body>

<header class="topbar reveal-item">
    <a href="index.php?action=review_pending" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=review_pending" class="active">
            Agendamentos Pendentes
        </a>

        <a href="index.php?action=barber_profile&id=<?= $idBarbeiro ?>">
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

<main class="page">

    <section class="review-container">

        <aside class="review-side reveal-item">
            <span class="side-tag">Painel de gestão</span>

            <h1>Meus agendamentos pendentes</h1>

            <p>
                Analise as solicitações feitas pelos seus clientes e confirme ou recuse
                cada atendimento conforme sua disponibilidade.
            </p>

            <div class="manager-box">
                <div class="manager-avatar">
                    <?= e($inicialBarbeiro) ?>
                </div>

                <div>
                    <span>Barbeiro logado</span>
                    <strong><?= e($barbeiroNome) ?></strong>
                </div>
            </div>

            <div class="info-box">
                <strong>Total pendente</strong>

                <p>
                    <?= $totalPendentes === 1
                        ? 'Existe 1 agendamento aguardando análise.'
                        : 'Existem ' . $totalPendentes . ' agendamentos aguardando análise.'
                    ?>
                </p>
            </div>
        </aside>

        <section class="review-content reveal-item">

            <div class="content-header">
                <span>BarberTime</span>

                <h2>Solicitações aguardando confirmação</h2>

                <p>
                    Confira os dados do cliente, barbeiro, serviços e horário antes
                    de aceitar ou recusar o agendamento.
                </p>
            </div>

            <?php if ($message === 'accepted'): ?>
                <div class="alert success-message reveal-item">
                    Agendamento aceito com sucesso.
                </div>
            <?php endif; ?>

            <?php if ($message === 'rejected'): ?>
                <div class="alert error-message reveal-item">
                    Agendamento recusado com sucesso.
                </div>
            <?php endif; ?>

            <?php if ($message === 'invalid'): ?>
                <div class="alert error-message reveal-item">
                    Agendamento inválido.
                </div>
            <?php endif; ?>

            <?php if ($message === 'error'): ?>
                <div class="alert error-message reveal-item">
                    Não foi possível realizar a operação.
                </div>
            <?php endif; ?>

            <?php if (!empty($agendamentos)): ?>

                <div class="appointments-list">

                    <?php foreach ($agendamentos as $index => $agendamento): ?>

                        <article class="appointment-card reveal-item micro-lift">

                            <div class="card-header">
                                <span class="step-number">
                                    <?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>
                                </span>

                                <div>
                                    <h3><?= e($agendamento['cliente_nome']) ?></h3>

                                    <p>
                                        Solicitação de atendimento aguardando confirmação.
                                    </p>
                                </div>
                            </div>

                            <div class="appointment-grid">

                                <div class="detail-item">
                                    <span>Serviços</span>
                                    <strong><?= e($agendamento['servicos'] ?? 'Serviço não informado') ?></strong>
                                </div>

                                <div class="detail-item">
                                    <span>Barbeiro</span>
                                    <strong><?= e($agendamento['barbeiro_nome'] ?? $barbeiroNome) ?></strong>
                                </div>

                                <div class="detail-item">
                                    <span>Data e horário</span>
                                    <strong>
                                        <?= date('d/m/Y H:i', strtotime($agendamento['data_hora'])) ?>
                                    </strong>
                                </div>

                                <div class="detail-item">
                                    <span>Status</span>
                                    <strong class="status-pill">
                                        <?= e(ucfirst($agendamento['status'] ?? 'pendente')) ?>
                                    </strong>
                                </div>

                            </div>

                            <div class="appointment-actions">

                                <a
                                    href="index.php?action=review_show&id=<?= (int) $agendamento['id_agendamento'] ?>"
                                    class="btn-review btn-details"
                                >
                                    <span class="btn-icon">👁</span>
                                    <span>Ver mais detalhes</span>
                                </a>

                                <form action="index.php?action=review_accept" method="POST" class="review-form">
                                    <input
                                        type="hidden"
                                        name="id_agendamento"
                                        value="<?= (int) $agendamento['id_agendamento'] ?>"
                                    >

                                    <button type="submit" class="btn-review btn-accept">
                                        <span class="btn-icon">✓</span>
                                        <span>Aceitar agendamento</span>
                                    </button>
                                </form>

                                <form action="index.php?action=review_reject" method="POST" class="review-form">
                                    <input
                                        type="hidden"
                                        name="id_agendamento"
                                        value="<?= (int) $agendamento['id_agendamento'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn-review btn-reject"
                                        onclick="return confirm('Deseja realmente recusar este agendamento?');"
                                    >
                                        <span class="btn-icon">×</span>
                                        <span>Recusar agendamento</span>
                                    </button>
                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <section class="empty-state reveal-item">
                    <div class="empty-icon">✓</div>

                    <h3>Nenhum agendamento pendente</h3>

                    <p>
                        No momento não existem solicitações aguardando análise.
                    </p>
                </section>

            <?php endif; ?>

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
            item.style.transitionDelay = `${index * 0.06}s`;
            observer.observe(item);
        });
    }

    ativarReveals();

    document.querySelectorAll('.review-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('button');

            if (!button) {
                return;
            }

            button.disabled = true;

            if (button.classList.contains('btn-accept')) {
                button.innerHTML = '<span class="btn-icon">...</span><span>Aceitando</span>';
            } else {
                button.innerHTML = '<span class="btn-icon">...</span><span>Recusando</span>';
            }
        });
    });
</script>

</body>
</html>