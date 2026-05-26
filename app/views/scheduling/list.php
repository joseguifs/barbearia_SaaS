<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clienteNomeSessao = $_SESSION['cliente_nome'] ?? $_SESSION['nome'] ?? 'Cliente';

$clienteInicial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($clienteNomeSessao, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($clienteNomeSessao, 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - BarberTime</title>

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css">

    <style>
        .appointments-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .appointment-card {
            padding: 22px;
            border-radius: 22px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
            transition: 0.25s ease;
        }

        .appointment-card:hover {
            border-color: rgba(197, 157, 95, 0.35);
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
        }

        .appointment-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .appointment-title h3 {
            color: #fff;
            font-size: 1.15rem;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .appointment-title p {
            color: var(--text-muted);
            line-height: 1.5;
        }

        .appointment-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
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

        .appointment-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-view {
            min-height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            transition: 0.25s ease;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            opacity: 0.96;
        }

        .empty-state {
            padding: 28px;
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

                /* =========================
        EFEITO IGUAL AO DA HOME
        ========================= */

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

            will-change: transform;
        }

        .micro-lift:hover {
            transform: translateY(-6px);
        }

        @media (max-width: 768px) {
            .appointment-top {
                flex-direction: column;
            }

            .appointment-info {
                grid-template-columns: 1fr;
            }

            .appointment-actions {
                justify-content: stretch;
            }

            .btn-view {
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
            <a href="index.php?action=scheduling_list" class="active">Meus Agendamentos</a>
            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </nav>
    </header>

    <main class="page">

        <section class="schedule-container">

            <aside class="schedule-side reveal-item">
                <span class="side-tag">Meus horários</span>

                <h1>Agendamentos ativos</h1>

                <p>
                    Veja seus agendamentos pendentes ou confirmados e acesse os detalhes de cada atendimento.
                </p>

                <div class="client-box micro-lift">
                    <div class="client-avatar">
                        <?= htmlspecialchars($clienteInicial) ?>
                    </div>

                    <div>
                        <span>Cliente logado</span>
                        <strong><?= htmlspecialchars($clienteNomeSessao) ?></strong>
                    </div>
                </div>

                <div class="info-box micro-lift">
                    <strong>Filtro aplicado</strong>
                    <p>
                        Esta tela mostra apenas agendamentos com status pendente ou agendado.
                    </p>
                </div>
            </aside>

            <section class="schedule-content reveal-item">
                <div class="content-header">
                    <span>BarberTime</span>
                    <h2>Meus agendamentos</h2>
                    <p>Selecione um agendamento para visualizar os detalhes completos.</p>
                </div>

                <div class="schedule-form">
                    <section id="loading-box" class="card reveal-item">
                        <p class="empty-message">Carregando seus agendamentos...</p>
                    </section>

                    <section id="erro-box" class="card reveal-item" style="display: none;">
                        <p class="empty-message" id="erro-texto">Não foi possível carregar os agendamentos.</p>
                    </section>

                    <section id="lista-box" class="appointments-list" style="display: none;"></section>
                </div>
            </section>

        </section>
    </main>



    <div class="logout-modal-overlay" id="logoutModal" aria-hidden="true">
        <div class="logout-modal">
            <div class="logout-modal-icon">!</div>

            <h2>Deseja realmente sair?</h2>

            <p>
                Você será desconectado da sua conta e precisará fazer login novamente
                para acessar seus agendamentos.
            </p>

            <div class="logout-modal-actions">
                <button type="button" class="btn-cancel-logout" id="cancelLogout">
                    Cancelar
                </button>

                <button type="button" class="btn-confirm-logout" id="confirmLogout">
                    Sair da conta
                </button>
            </div>
        </div>
    </div>

    <script>
        const loadingBox = document.getElementById('loading-box');
        const erroBox = document.getElementById('erro-box');
        const erroTexto = document.getElementById('erro-texto');
        const listaBox = document.getElementById('lista-box');

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

        function formatarStatus(status) {
            const nomes = {
                pendente: 'Pendente',
                agendado: 'Agendado',
                checked: 'Confirmado'
            };

            return nomes[status] || status || '-';
        }

        function formatarDataHora(dataHora) {
            if (!dataHora) {
                return '-';
            }

            const data = new Date(dataHora.replace(' ', 'T'));

            if (isNaN(data.getTime())) {
                return dataHora;
            }

            return data.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatarMoeda(valor) {
            return Number(valor || 0).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function esconderLoading() {
            loadingBox.style.display = 'none';
        }

        function mostrarErro(mensagem) {
            esconderLoading();

            listaBox.style.display = 'none';
            erroBox.style.display = 'block';
            erroTexto.textContent = mensagem;

            ativarReveals(erroBox);
        }

        function montarCard(agendamento) {
            const status = agendamento.status || 'pendente';
            const servicos = agendamento.servicos_texto || 'Serviço não informado';
            const valor = agendamento.valor_total || 0;

            return `
                <article class="appointment-card micro-lift reveal-item">
                    <div class="appointment-top">
                        <div class="appointment-title">
                            <h3>${servicos}</h3>
                            <p>Atendimento com ${agendamento.barbeiro_nome || 'barbeiro não informado'}.</p>
                        </div>

                        <span class="status-badge status-${status}">
                            ${formatarStatus(status)}
                        </span>
                    </div>

                    <div class="appointment-info">
                        <div class="info-item">
                            <span>Data e horário</span>
                            <strong>${formatarDataHora(agendamento.data_hora)}</strong>
                        </div>

                        <div class="info-item">
                            <span>Valor</span>
                            <strong>${formatarMoeda(valor)}</strong>
                        </div>
                    </div>

                    <div class="appointment-actions">
                        <a 
                            href="index.php?action=scheduling_get&id=${agendamento.id_agendamento}" 
                            class="btn-view"
                        >
                            Ver agendamento
                        </a>
                    </div>
                </article>
            `;
        }

        function mostrarLista(agendamentos) {
            esconderLoading();

            erroBox.style.display = 'none';
            listaBox.style.display = 'flex';

            if (!agendamentos || agendamentos.length === 0) {
                listaBox.innerHTML = `
                    <div class="empty-state micro-lift reveal-item">
                        <h3>Nenhum agendamento ativo</h3>
                        <p>Você ainda não possui agendamentos pendentes ou confirmados.</p>

                        <a href="index.php?action=scheduling_create" class="btn-view">
                            Criar agendamento
                        </a>
                    </div>
                `;

                ativarReveals(listaBox);
                return;
            }

            listaBox.innerHTML = agendamentos.map(montarCard).join('');

            ativarReveals(listaBox);
        }

        async function carregarAgendamentos() {
            try {
                const response = await fetch('index.php?action=api_scheduling_my_active', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const responseText = await response.text();

                let resultado;

                try {
                    resultado = JSON.parse(responseText);
                } catch (error) {
                    console.error(responseText);
                    mostrarErro('A API não retornou uma resposta válida.');
                    return;
                }

                if (response.status === 401) {
                    window.location.href = 'index.php?action=login';
                    return;
                }

                if (!response.ok || !resultado.success) {
                    mostrarErro(resultado.message || 'Não foi possível carregar seus agendamentos.');
                    return;
                }

                mostrarLista(resultado.data || []);

            } catch (error) {
                console.error(error);
                mostrarErro('Erro ao conectar com a API de agendamentos.');
            }
        }

        ativarReveals();
        carregarAgendamentos();
    </script>



    <script>
        const logoutLinks = document.querySelectorAll('.btn-logout');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        let logoutUrl = 'index.php?action=logout';

        function openLogoutModal(url) {
            logoutUrl = url || 'index.php?action=logout';

            if (logoutModal) {
                logoutModal.classList.add('show');
                logoutModal.setAttribute('aria-hidden', 'false');
            }
        }

        function closeLogoutModal() {
            if (logoutModal) {
                logoutModal.classList.remove('show');
                logoutModal.setAttribute('aria-hidden', 'true');
            }
        }

        logoutLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                openLogoutModal(link.getAttribute('href'));
            });
        });

        if (cancelLogout) {
            cancelLogout.addEventListener('click', function () {
                closeLogoutModal();
            });
        }

        if (confirmLogout) {
            confirmLogout.addEventListener('click', function () {
                confirmLogout.disabled = true;
                confirmLogout.textContent = 'SAINDO...';

                window.location.href = logoutUrl;
            });
        }

        if (logoutModal) {
            logoutModal.addEventListener('click', function (event) {
                if (event.target === logoutModal) {
                    closeLogoutModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>



</body>
</html>