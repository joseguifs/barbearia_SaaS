<?php
if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idClienteSessao =
    $_SESSION['id_cliente']
    ?? $_SESSION['cliente']['id_cliente']
    ?? $_SESSION['user']['id_cliente']
    ?? $_SESSION['cliente_id']
    ?? null;

$perfilUrl = $idClienteSessao
    ? 'index.php?action=profile&id=' . urlencode($idClienteSessao)
    : 'index.php?action=profile';

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
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/home-full.css">
</head>

<body>
    <header class="topbar reveal-item">
        <div class="logo">BARBERTIME</div>

        <div class="topbar-right">
            <nav class="nav">
                <a href="#agendamentos">Agendamentos</a>
                <a href="#destaques">Serviços</a>
                <a href="#estilos">Estilos</a>
                <a href="index.php?action=scheduling_history">Histórico</a>
                <a href="<?= e($perfilUrl) ?>">Perfil</a>
            </nav>

            <button type="button" id="logout-button" class="logout-btn" title="Sair" aria-label="Sair">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10 17V15H4V9H10V7H4C2.9 7 2 7.9 2 9V15C2 16.1 2.9 17 4 17H10ZM13 19L21 12L13 5V10H8V14H13V19Z"/>
                </svg>
            </button>
        </div>
    </header>

    <section class="hero reveal-item">
        <div class="hero-overlay"></div>

        <div class="hero-content reveal-item">
            <h1 id="greeting-title">Olá, <?= e($clienteNome) ?></h1>
            <p id="greeting-subtitle">Bem vindo ao painel da barbearia BarberTime</p>

            <a href="index.php?action=scheduling_create" class="hero-btn">Fazer agendamento</a>
        </div>
    </section>

    <main class="home-page">
        <section class="appointments-section reveal-item" id="agendamentos">
             <h2>Agendamentos</h2>
                <div class="home-scheduling-card micro-lift reveal-item">
                    <div>
                        <span class="section-tag">Meus horários</span>

                        <h3>Acompanhe seus agendamentos</h3>

                        <p>
                            Veja seus agendamentos pendentes ou confirmados e acompanhe os detalhes do atendimento.
                        </p>
                    </div>

                    <a href="index.php?action=scheduling_list" class="btn-see-appointments">
                        Ver agendamentos
                    </a>
                </div>
            </section>
            <h2>Serviços em Destaque</h2>

            <div class="services-grid">
                <article class="service-card micro-lift reveal-item">
                    <img src="/BARBEARIA_SAAS/public/assets/images/degradelowfade.png"
                        alt="Serviço cabelo e barba">
                    <div class="service-body">
                        <h3>Cabelo + Barba</h3>
                        <p>Skin Fade</p>
                        <small>50 min · Hidratação e penteado</small>
                        <p>Barba</p>
                        <small>40 min · Tesoura e aparador</small>
                        <a href="index.php?action=scheduling_create" class="mini-btn">Agendar</a>
                    </div>
                </article>

                <article class="service-card micro-lift reveal-item">
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

                <article class="service-card micro-lift reveal-item">
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

        <section class="styles-section reveal-item" id="estilos">
            <h2>Estilos</h2>

            <div class="styles-grid">
                <article class="style-card micro-lift reveal-item">
                    <img src="/BARBEARIA_SAAS/public/assets/images/buzzcut.jpg" alt="Buzz cut">
                    <h3>Buzz cut</h3>
                    <p>Um corte curto, prático e moderno, ideal para quem busca um visual limpo, discreto e fácil de manter.</p>
                </article>

                <article class="style-card micro-lift reveal-item">
                    <img src="/BARBEARIA_SAAS/public/assets/images/executivo.jpg" alt="Executivo">
                    <h3>Executivo</h3>
                    <p>Um corte clássico, elegante e bem alinhado, ideal para quem busca uma aparência profissional e sofisticada.</p>
                </article>

                <article class="style-card micro-lift reveal-item">
                    <img src="/BARBEARIA_SAAS/public/assets/images/mohawk.jpg" alt="Mohawk">
                    <h3>Mohawk</h3>
                    <p>Um corte moderno e marcante, com laterais mais baixas e destaque no volume central do cabelo.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer reveal-item">
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

    <div id="logout-modal-backdrop" class="logout-modal-backdrop" aria-hidden="true">
        <div class="logout-modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title">
            <h3 id="logout-modal-title">Deseja realmente sair?</h3>
            <p>Você será desconectado da sua conta atual e voltará para a tela de login.</p>

            <div class="logout-modal-actions">
                <button type="button" id="cancel-logout" class="logout-modal-btn cancel">Cancelar</button>
                <button type="button" id="confirm-logout" class="logout-modal-btn confirm">Sair da conta</button>
            </div>
        </div>
    </div>

    <div id="toast-message" class="toast-message"></div>

    <script>
        const logoutButton = document.getElementById('logout-button');
        const logoutModalBackdrop = document.getElementById('logout-modal-backdrop');
        const cancelLogoutButton = document.getElementById('cancel-logout');
        const confirmLogoutButton = document.getElementById('confirm-logout');
        const toastMessage = document.getElementById('toast-message');
        const greetingTitle = document.getElementById('greeting-title');
        const greetingSubtitle = document.getElementById('greeting-subtitle');

        function showToast(message) {
            toastMessage.textContent = message;
            toastMessage.classList.add('show');

            setTimeout(() => {
                toastMessage.classList.remove('show');
            }, 2600);
        }

        function abrirModalLogout() {
            logoutModalBackdrop.classList.add('show');
            logoutModalBackdrop.setAttribute('aria-hidden', 'false');
        }

        function fecharModalLogout() {
            logoutModalBackdrop.classList.remove('show');
            logoutModalBackdrop.setAttribute('aria-hidden', 'true');
        }

        function atualizarSaudacao() {
            const hora = new Date().getHours();
            let saudacao = 'Olá';

            if (hora >= 5 && hora < 12) {
                saudacao = 'Bom dia';
            } else if (hora >= 12 && hora < 18) {
                saudacao = 'Boa tarde';
            } else {
                saudacao = 'Boa noite';
            }

            greetingTitle.textContent = `${saudacao}, <?= e($clienteNome) ?>`;
            greetingSubtitle.textContent = 'Bem-vindo ao painel da barbearia BarberTime';
        }

        function ativarReveals() {
            const itens = document.querySelectorAll('.reveal-item');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.12
            });

            itens.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.05}s`;
                observer.observe(item);
            });
        }

        function ativarLinksSuaves() {
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', function (event) {
                    const targetId = this.getAttribute('href');

                    if (!targetId || targetId === '#') {
                        return;
                    }

                    const target = document.querySelector(targetId);

                    if (target) {
                        event.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        }

        logoutButton.addEventListener('click', function () {
            abrirModalLogout();
        });

        cancelLogoutButton.addEventListener('click', function () {
            fecharModalLogout();
        });

        logoutModalBackdrop.addEventListener('click', function (event) {
            if (event.target === logoutModalBackdrop) {
                fecharModalLogout();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && logoutModalBackdrop.classList.contains('show')) {
                fecharModalLogout();
            }
        });

        confirmLogoutButton.addEventListener('click', async function () {
            logoutButton.disabled = true;
            logoutButton.classList.add('is-loading');
            confirmLogoutButton.disabled = true;
            confirmLogoutButton.textContent = 'Saindo...';

            try {
                const response = await fetch('index.php?action=api_auth_logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível sair da conta.');
                    logoutButton.disabled = false;
                    logoutButton.classList.remove('is-loading');
                    confirmLogoutButton.disabled = false;
                    confirmLogoutButton.textContent = 'Sair da conta';
                    fecharModalLogout();
                    return;
                }

                window.location.href = 'index.php?action=login';
            } catch (error) {
                console.error(error);
                showToast('Erro ao realizar logout.');
                logoutButton.disabled = false;
                logoutButton.classList.remove('is-loading');
                confirmLogoutButton.disabled = false;
                confirmLogoutButton.textContent = 'Sair da conta';
                fecharModalLogout();
            }
        });

        atualizarSaudacao();
        ativarReveals();
        ativarLinksSuaves();
    </script>
</body>

</html>