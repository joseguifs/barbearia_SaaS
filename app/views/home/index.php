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

    <style>

    html{
        scroll-behavior:smooth;
    }

    body{
        background:#0f0f0f;
    }

    /* =========================================
    TOPBAR
    ========================================= */

    .topbar{
        position:fixed;

        top:0;
        left:0;

        width:100%;
        height:82px;

        padding:0 42px;

        display:flex;
        align-items:center;
        justify-content:space-between;

        z-index:1000;

        background:rgba(10,10,10,.52);

        backdrop-filter:blur(18px);
        -webkit-backdrop-filter:blur(18px);

        border-bottom:1px solid rgba(255,255,255,.06);

        box-shadow:
            0 12px 40px rgba(0,0,0,.35),
            inset 0 1px 0 rgba(255,255,255,.03);

        transition:
            background .25s ease,
            border-color .25s ease;
    }

    .topbar::before{
        content:"";

        position:absolute;
        inset:0;

        background:
            linear-gradient(
                90deg,
                rgba(197,157,95,.08),
                transparent 20%,
                transparent 80%,
                rgba(197,157,95,.05)
            );

        pointer-events:none;
    }

    .logo{
        position:relative;

        display:flex;
        align-items:center;
        gap:12px;

        font-size:1.5rem;
        font-weight:800;

        letter-spacing:2px;
        color:#fff;

        z-index:2;
    }

    .logo::before{
        content:"";

        width:10px;
        height:10px;

        border-radius:50%;

        background:#c59d5f;

        box-shadow:
            0 0 14px rgba(197,157,95,.8);
    }

    .topbar-right{
        position:relative;

        display:flex;
        align-items:center;
        gap:18px;

        z-index:2;
    }

    .nav{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .nav a{
        position:relative;

        text-decoration:none;

        color:rgba(255,255,255,.72);

        padding:11px 18px;

        border-radius:14px;

        font-size:.92rem;
        font-weight:600;

        transition:
            color .25s ease,
            background .25s ease,
            transform .25s ease,
            border-color .25s ease;

        border:1px solid transparent;
    }

    .nav a:hover{
        color:#fff;

        background:rgba(255,255,255,.06);

        border-color:rgba(255,255,255,.06);

        transform:translateY(-1px);
    }

    .logout-btn{
        width:48px;
        height:48px;

        display:flex;
        align-items:center;
        justify-content:center;

        border:none;
        border-radius:14px;

        background:rgba(255,255,255,.05);

        border:1px solid rgba(255,255,255,.08);

        cursor:pointer;

        transition:
            transform .25s ease,
            background .25s ease,
            border-color .25s ease,
            box-shadow .25s ease;
    }

    .logout-btn:hover{
        transform:translateY(-2px);

        background:rgba(197,157,95,.10);

        border-color:rgba(197,157,95,.22);

        box-shadow:
            0 10px 25px rgba(0,0,0,.20);
    }

    .logout-btn svg{
        width:22px;
        height:22px;

        fill:#fff;
    }

    /* =========================================
    HERO
    ========================================= */

    .hero{
        position:relative;
        min-height:92vh;

        display:flex;
        align-items:center;
        justify-content:center;

        overflow:hidden;

        padding-top:82px;
    }

    .hero::before{
        content:"";
        position:absolute;
        inset:0;

        background:
            linear-gradient(
                rgba(0,0,0,.40),
                rgba(0,0,0,.62)
            ),
            url('/BARBEARIA_SAAS/public/assets/images/backgroundLogin.jpeg')
            center center/cover no-repeat;

        transform:scale(1.06);

        filter:
            blur(3px)
            brightness(.55);
    }

    .hero-overlay{
        position:absolute;
        inset:0;

        background:
            radial-gradient(
                circle at top right,
                rgba(197,157,95,.14),
                transparent 35%
            );
    }

    .hero-content{
        position:relative;
        z-index:2;

        text-align:center;

        max-width:760px;

        padding:40px;
    }

    .hero-content h1{
        font-size:4rem;
        line-height:1.1;

        color:#fff;

        margin-bottom:18px;

        font-weight:800;
    }

    .hero-content p{
        color:rgba(255,255,255,.78);

        font-size:1.1rem;
        line-height:1.7;

        margin-bottom:34px;
    }

    .hero-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;

        height:58px;

        padding:0 34px;

        border-radius:16px;

        text-decoration:none;

        background:
            linear-gradient(
                135deg,
                #c59d5f,
                #8b5e34
            );

        color:#fff;

        font-weight:700;
        letter-spacing:.5px;

        transition:
            transform .25s ease,
            box-shadow .25s ease,
            opacity .25s ease;
    }

    .hero-btn:hover{
        transform:translateY(-3px);

        opacity:.97;

        box-shadow:
            0 15px 35px rgba(197,157,95,.22);
    }

    /* =========================================
    CARDS
    ========================================= */

    .next-card,
    .service-card,
    .style-card{
        background:rgba(18,18,18,.42);

        backdrop-filter:blur(16px);
        -webkit-backdrop-filter:blur(16px);

        border:1px solid rgba(255,255,255,.08);

        box-shadow:
            0 20px 50px rgba(0,0,0,.22),
            inset 0 1px 0 rgba(255,255,255,.03);

        border-radius:26px;

        transition:
            transform .3s ease,
            border-color .3s ease,
            box-shadow .3s ease;
    }

    .next-card:hover,
    .service-card:hover,
    .style-card:hover{
        border-color:rgba(197,157,95,.18);

        box-shadow:
            0 24px 60px rgba(0,0,0,.28),
            0 0 0 1px rgba(197,157,95,.05);
    }

    .service-card img,
    .style-card img{
        border-top-left-radius:26px;
        border-top-right-radius:26px;
    }

    /* =========================================
    TÍTULOS
    ========================================= */

    .appointments-section h2,
    .highlight-section h2,
    .styles-section h2{
        font-size:2rem;
        color:#fff;

        margin-bottom:28px;

        position:relative;
    }

    .appointments-section h2::after,
    .highlight-section h2::after,
    .styles-section h2::after{
        content:"";

        display:block;

        width:70px;
        height:4px;

        margin-top:12px;

        border-radius:999px;

        background:
            linear-gradient(
                90deg,
                #c59d5f,
                transparent
            );
    }

    /* =========================================
    FOOTER
    ========================================= */

    .footer{
        padding:70px 20px 50px;

        border-top:1px solid rgba(255,255,255,.06);

        background:
            linear-gradient(
                to bottom,
                rgba(15,15,15,.92),
                rgba(10,10,10,1)
            );
    }

    .footer-container{
        max-width:1280px;
        margin:0 auto;

        display:flex;
        align-items:center;
        justify-content:space-between;

        gap:28px;

        flex-wrap:wrap;
    }

    .footer-logo{
        font-size:1.3rem;
        font-weight:800;
        letter-spacing:2px;

        color:#fff;
    }

    .footer-copy{
        color:rgba(255,255,255,.62);
    }

    .footer-links{
        display:flex;
        gap:18px;
        flex-wrap:wrap;
    }

    .footer-links a{
        text-decoration:none;

        color:rgba(255,255,255,.72);

        transition:.25s ease;
    }

    .footer-links a:hover{
        color:#e7c48d;
    }

    /* =========================================
    REVEALS
    ========================================= */

    .reveal-item{
        opacity:0;
        transform:translateY(26px);

        transition:
            opacity .7s ease,
            transform .7s ease;
    }

    .reveal-item.is-visible{
        opacity:1;
        transform:translateY(0);
    }

    .micro-lift{
        transition:
            transform .28s ease,
            box-shadow .28s ease;

        will-change:transform;
    }

    .micro-lift:hover{
        transform:translateY(-6px);
    }

    /* =========================================
    MODAL LOGOUT
    ========================================= */

    .logout-modal-backdrop{
        position:fixed;
        inset:0;

        background:rgba(0,0,0,.65);

        backdrop-filter:blur(6px);
        -webkit-backdrop-filter:blur(6px);

        display:flex;
        align-items:center;
        justify-content:center;

        padding:20px;

        z-index:9999;

        opacity:0;
        pointer-events:none;

        transition:opacity .25s ease;
    }

    .logout-modal-backdrop.show{
        opacity:1;
        pointer-events:auto;
    }

    .logout-modal{
        width:100%;
        max-width:430px;

        background:rgba(18,18,18,.46);

        backdrop-filter:blur(18px);
        -webkit-backdrop-filter:blur(18px);

        border:1px solid rgba(255,255,255,.08);

        border-radius:24px;

        padding:28px;

        box-shadow:
            0 25px 60px rgba(0,0,0,.45),
            inset 0 1px 0 rgba(255,255,255,.04);

        color:#fff;

        transform:translateY(12px) scale(.97);

        transition:
            transform .25s ease,
            opacity .25s ease;
    }

    .logout-modal-backdrop.show .logout-modal{
        transform:translateY(0) scale(1);
    }

    .logout-modal h3{
        font-size:1.45rem;
        margin-bottom:12px;

        color:#fff;
    }

    .logout-modal p{
        color:rgba(255,255,255,.72);

        line-height:1.6;
    }

    .logout-modal-actions{
        display:flex;
        gap:12px;

        margin-top:26px;
    }

    .logout-modal-btn{
        flex:1;

        height:50px;

        border:none;
        border-radius:14px;

        font-weight:700;

        cursor:pointer;

        transition:
            transform .2s ease,
            opacity .2s ease,
            background .25s ease;
    }

    .logout-modal-btn:hover{
        transform:translateY(-2px);
    }

    .logout-modal-btn.cancel{
        background:rgba(255,255,255,.06);

        border:1px solid rgba(255,255,255,.08);

        color:#fff;
    }

    .logout-modal-btn.cancel:hover{
        background:rgba(255,255,255,.10);
    }

    .logout-modal-btn.confirm{
        background:
            linear-gradient(
                135deg,
                #c59d5f,
                #8b5e34
            );

        color:#fff;
    }

    .logout-modal-btn.confirm:hover{
        opacity:.96;

        box-shadow:
            0 10px 25px rgba(197,157,95,.22);
    }

    /* =========================================
    TOAST
    ========================================= */

    .toast-message{
        position:fixed;

        right:20px;
        bottom:20px;

        min-width:260px;
        max-width:360px;

        padding:16px 18px;

        border-radius:16px;

        background:rgba(18,18,18,.52);

        backdrop-filter:blur(18px);
        -webkit-backdrop-filter:blur(18px);

        border:1px solid rgba(255,255,255,.08);

        color:#fff;

        box-shadow:
            0 20px 40px rgba(0,0,0,.35);

        z-index:10000;

        opacity:0;
        transform:translateY(12px);

        transition:
            opacity .25s ease,
            transform .25s ease;
    }

    .toast-message.show{
        opacity:1;
        transform:translateY(0);
    }

    /* =========================================
    RESPONSIVO
    ========================================= */

    @media(max-width:900px){

        .topbar{
            padding:0 18px;
            height:74px;
        }

        .nav{
            display:none;
        }

        .hero-content h1{
            font-size:3rem;
        }
    }

    @media(max-width:600px){

        .topbar{
            height:70px;
            padding:0 14px;
        }

        .logo{
            font-size:1.05rem;
            letter-spacing:1.5px;
        }

        .hero{
            padding-top:70px;
        }

        .hero-content{
            padding:20px;
        }

        .hero-content h1{
            font-size:2.2rem;
        }

        .hero-content p{
            font-size:.98rem;
        }

        .hero-btn{
            width:100%;
        }

        .footer-container{
            flex-direction:column;
            align-items:flex-start;
        }

        .logout-modal-actions{
            flex-direction:column;
        }

        .toast-message{
            left:16px;
            right:16px;

            max-width:none;
            min-width:0;
        }
    }


    .home-scheduling-card {
    margin: 40px auto;
    max-width: 1100px;
    padding: 28px;
    border-radius: 22px;
    background: rgba(18, 18, 18, 0.62);
    border: 1px solid rgba(197, 157, 95, 0.25);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    
    }

    .home-scheduling-card h2 {
        color: #fff;
        margin: 8px 0;
    }

    .home-scheduling-card p {
        color: rgba(255, 255, 255, 0.72);
    }

    .section-tag {
        color: #c59d5f;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-see-appointments {
        padding: 14px 22px;
        border-radius: 14px;
        text-decoration: none;
        color: #fff;
        font-weight: bold;
        background: linear-gradient(135deg, #c59d5f, #8b5e34);
        white-space: nowrap;
        transition: 0.25s ease;
    }

    .btn-see-appointments:hover {
        transform: translateY(-2px);
        opacity: 0.96;
    }

    @media (max-width: 768px) {
        .home-scheduling-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-see-appointments {
            width: 100%;
            text-align: center;
        }
    }

</style>
</head>

<body>
    <header class="topbar reveal-item">
        <div class="logo">BARBERTIME</div>

        <div class="topbar-right">
            <nav class="nav">
                <a href="#agendamentos">Agendamentos</a>
                <a href="#destaques">Serviços</a>
                <a href="#estilos">Estilos</a>
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