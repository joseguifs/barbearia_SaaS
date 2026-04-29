<?php
if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$clienteNome = $clienteNome ?? 'Usuário';
$proximoAgendamento = $proximoAgendamento ?? null;

function formatarDataHome($dataHora)
{
    if (!$dataHora) {
        return 'Sem agendamento';
    }

    $dias = [
        'Sun' => 'Dom',
        'Mon' => 'Seg',
        'Tue' => 'Ter',
        'Wed' => 'Qua',
        'Thu' => 'Qui',
        'Fri' => 'Sex',
        'Sat' => 'Sáb'
    ];

    $timestamp = strtotime($dataHora);
    $diaSemana = $dias[date('D', $timestamp)] ?? date('D', $timestamp);
    $dia = date('d', $timestamp);
    $mes = date('M', $timestamp);
    $hora = date('H:i', $timestamp);

    return "{$diaSemana}, {$dia} de {$mes} - {$hora}";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Home - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/home.css">
</head>

<body>
   <header class="topbar">
    <div class="logo">BARBERTIME</div>

    <div class="topbar-right">
        <nav class="nav">
            <a href="#">Serviços</a>
            <a href="#">Histórico</a>
            <a href="#">Perfil</a>
            <a href="#">Barbeiros</a>
        </nav>

        <a href="index.php?action=logout" class="logout-btn" title="Sair" aria-label="Sair">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 17V15H4V9H10V7H4C2.9 7 2 7.9 2 9V15C2 16.1 2.9 17 4 17H10ZM13 19L21 12L13 5V10H8V14H13V19Z"/>
            </svg>
        </a>
    </div>
</header>

    <section class="hero">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1>Olá, <?= e($clienteNome) ?></h1>
            <p>Bem vindo ao painel da barbearia BarberTime</p>

            <a href="index.php?action=scheduling_create" class="hero-btn">Marcar agendamento</a>
        </div>
    </section>

    <main class="home-page">
        <section class="appointments-section">
            <h2>Agendamentos</h2>

            <div class="next-card">
                <div class="next-card-left">
                    <div class="calendar-icon">
                        <span class="calendar-top"></span>
                        <span class="calendar-body"></span>
                        <span class="calendar-dot dot-1"></span>
                        <span class="calendar-dot dot-2"></span>
                        <span class="calendar-dot dot-3"></span>
                        <span class="calendar-dot dot-4"></span>
                    </div>

                    <div class="next-card-info">
                        <small>Seu próximo agendamento</small>

                        <?php if ($proximoAgendamento): ?>
                            <strong><?= e(formatarDataHome($proximoAgendamento['data_hora'])) ?></strong>
                            <span><?= e($proximoAgendamento['servicos_texto'] ?? 'Serviço não informado') ?> com
                                <?= e($proximoAgendamento['barbeiro_nome'] ?? 'Barbeiro') ?></span>
                        <?php else: ?>
                            <strong>Sem horário marcado</strong>
                            <span>Faça seu próximo agendamento</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($proximoAgendamento): ?>
                    <a href="index.php?action=scheduling_edit&id=<?= e($proximoAgendamento['id_agendamento']) ?>"
                        class="outline-btn">
                        Alterar agendamento
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <section class="highlight-section">
            <h2>Serviços em Destaque</h2>

            <div class="services-grid">
                <article class="service-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/degradelowfade.png"
                        alt="Serviço cabelo e sobrancelha">
                    <div class="service-body">
                        <h3>Cabelo + Sobrancelha</h3>
                        <p>Skin Fade</p>
                        <small>50 min · Hidratação e penteado</small>
                        <p>Sobrancelha</p>
                        <small>40 min · Tesoura e aparador</small>
                        <a href="index.php?action=scheduling_create" class="mini-btn">Agendar</a>
                    </div>
                </article>

                <article class="service-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/barbafull.png" alt="Serviço barba">
                    <div class="service-body">
                        <h3>Barba</h3>
                        <p>Full Beard</p>
                        <small>35 min · Modelagem e estilização</small>
                        <p>Barba italiana</p>
                        <small>40 min · Modelagem e estilização</small>
                        <a href="index.php?action=scheduling_create" class="mini-btn">Agendar</a>
                    </div>
                </article>

                <article class="service-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/executivob.png" alt="Serviço cabelo">
                    <div class="service-body">
                        <h3>Cabelo</h3>
                        <p>Corte executivo</p>
                        <small>50 min · Hidratação e penteado</small>
                        <p>Skin Fade</p>
                        <small>40 min · Hidratação e penteado</small>
                        <a href="index.php?action=scheduling_create" class="mini-btn">Agendar</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="styles-section">
            <h2>Estilos</h2>

            <div class="styles-grid">
                <article class="style-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/buzzcut.jpg" alt="Buzz cut">
                    <h3>Buzz cut</h3>
                    <p>Um corte curto, prático e moderno, ideal para quem busca um visual limpo, discreto e fácil de
                        manter.</p>
                </article>

                <article class="style-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/executivo.jpg" alt="Executivo">
                    <h3>Executivo</h3>
                    <p>Um corte clássico, elegante e bem alinhado, ideal para quem busca uma aparência profissional e
                        sofisticada.</p>
                </article>

                <article class="style-card">
                    <img src="/BARBEARIA_SAAS/public/assets/images/mohawk.jpg" alt="Mohawk">
                    <h3>Mohawk</h3>
                    <p>Um corte moderno e marcante, com laterais mais baixas e destaque no volume central do cabelo.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">BARBERTIME</div>

        <p class="footer-copy">
            © Barbertime cortes & barba. Criada para o homem moderno.
        </p>

        <nav class="footer-links">
            <a href="#">Política de Privacidade</a>
            <a href="#">Termos de Uso</a>
            <a href="#">Dúvidas frequentes</a>
            <a href="#">Contato</a>
        </nav>
    </div>
</footer>
</body>

</html>