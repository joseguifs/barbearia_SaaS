<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idAgendamento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idAgendamento) {
    $idAgendamento = 0;
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
    <title>Detalhes do Agendamento - BarberTime</title>

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css">

    <style>
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 16px;
        }

        .detail-box {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
        }

        .detail-box.full {
            grid-column: 1 / -1;
        }

        .detail-label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .detail-value {
            color: #fff;
            font-size: 1rem;
            line-height: 1.5;
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .status-pendente {
            color: #ffd27d;
            background: rgba(255, 193, 7, 0.12);
            border-color: rgba(255, 193, 7, 0.32);
        }

        .status-agendado {
            color: #b9f6ca;
            background: rgba(76, 175, 80, 0.14);
            border-color: rgba(76, 175, 80, 0.35);
        }

        .status-cancelado {
            color: #ffcdd2;
            background: rgba(244, 67, 54, 0.14);
            border-color: rgba(244, 67, 54, 0.35);
        }

        .status-concluido {
            color: #bbdefb;
            background: rgba(33, 150, 243, 0.14);
            border-color: rgba(33, 150, 243, 0.35);
        }

        .loading-card,
        .state-card {
            padding: 28px;
            border-radius: 22px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
            text-align: center;
        }

        .loading-card {
            color: var(--text-muted);
            font-weight: bold;
        }

        .state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(197, 157, 95, 0.12);
            border: 1px solid rgba(197, 157, 95, 0.35);
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: bold;
        }

        .state-card h3 {
            color: #fff;
            font-size: 1.45rem;
            margin-bottom: 10px;
        }

        .state-card p {
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 520px;
            margin: 0 auto;
        }

        .details-actions {
            margin-top: 22px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .details-actions.three-actions {
            grid-template-columns: 160px 1fr 1fr;
        }

        .btn-change {
            min-height: 54px;
            border-radius: 16px;

            display: flex;
            align-items: center;
            justify-content: center;

            text-decoration: none;

            color: #f5d7a2;
            font-size: 0.95rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;

            border: 1px solid rgba(197, 157, 95, 0.45);
            background: rgba(197, 157, 95, 0.08);

            transition: 0.25s ease;
        }

        .btn-change:hover {
            background: rgba(197, 157, 95, 0.14);
            color: #fff;
            transform: translateY(-2px);
        }


        .schedule-detail-actions {
            grid-template-columns: 140px 1fr 1fr 1fr;
        }

        .btn-cancel-schedule {
            min-height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(244, 67, 54, 0.45);
            background: rgba(244, 67, 54, 0.10);
            color: #ffcdd2;
            font-size: 0.95rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .btn-cancel-schedule:hover {
            background: rgba(244, 67, 54, 0.18);
            color: #fff;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .schedule-detail-actions {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .details-actions,
            .details-actions.three-actions {
                grid-template-columns: 1fr;
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
            <a href="index.php?action=scheduling_get&id=<?= (int)$idAgendamento ?>" class="active">Meu Agendamento</a>
            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </nav>
    </header>

    <main class="page">

        <section class="schedule-container">

            <aside class="schedule-side reveal-item">
                <span class="side-tag">Consulta de agendamento</span>

                <h1>Detalhes do atendimento</h1>

                <p>
                    Confira as informações do seu agendamento. Apenas agendamentos
                    vinculados ao cliente logado podem ser visualizados.
                </p>

                <div class="client-box">
                    <div class="client-avatar">
                        <?= htmlspecialchars($clienteInicial) ?>
                    </div>

                    <div>
                        <span>Cliente logado</span>
                        <strong><?= htmlspecialchars($clienteNomeSessao) ?></strong>
                    </div>
                </div>

                <div class="info-box">
                    <strong>Segurança</strong>
                    <p>
                        A API verifica se o agendamento pertence ao usuário da sessão
                        antes de exibir qualquer informação.
                    </p>
                </div>
            </aside>

            <section class="schedule-content reveal-item">

                <div class="content-header">
                    <span>BarberTime</span>
                    <h2>Informações do agendamento</h2>
                    <p>Visualize status, barbeiro, serviços, data e observações do atendimento.</p>
                </div>

                <div class="schedule-form">

                    <section id="loading-box" class="loading-card reveal-item">
                        Carregando informações do agendamento...
                    </section>

                    <section id="erro-box" class="state-card reveal-item" style="display: none;">
                        <div class="state-icon">!</div>

                        <h3>Acesso indisponível</h3>

                        <p id="erro-texto">
                            Não foi possível carregar este agendamento.
                        </p>

                        <div class="details-actions">
                            <a href="index.php?action=home" class="btn-secondary">
                                Voltar
                            </a>

                            <a href="index.php?action=scheduling_create" class="btn-confirmar">
                                Novo agendamento
                            </a>
                        </div>
                    </section>

                    <section id="agendamento-conteudo" class="card reveal-item micro-lift" style="display: none;">
                        <div class="card-header">
                            <span class="step-number">01</span>

                            <div>
                                <h3>Resumo do agendamento</h3>
                                <p>Dados encontrados para o agendamento solicitado.</p>
                            </div>
                        </div>

                        <div class="details-grid">
                            <div class="detail-box">
                                <span class="detail-label">Status</span>
                                <p class="detail-value">
                                    <span id="status-badge" class="status-badge"></span>
                                </p>
                            </div>

                            <div class="detail-box reveal-item micro-lift">
                                <span class="detail-label">Valor</span>
                                <p class="detail-value" id="valor-total">-</p>
                            </div>

                            <div class="detail-box full reveal-item micro-lift">
                                <span class="detail-label">Serviço(s)</span>
                                <p class="detail-value" id="servico-texto">-</p>
                            </div>

                            <div class="detail-box reveal-item micro-lift">
                                <span class="detail-label">Barbeiro</span>
                                <p class="detail-value" id="barbeiro-nome">-</p>
                            </div>

                            <div class="detail-box reveal-item micro-lift">
                                <span class="detail-label">Cliente</span>
                                <p class="detail-value" id="cliente-nome">-</p>
                            </div>

                            <div class="detail-box full reveal-item micro-lift">
                                <span class="detail-label">Data e horário</span>
                                <p class="detail-value" id="data-hora">-</p>
                            </div>

                            <div class="detail-box full reveal-item micro-lift" id="observacoes-box" style="display: none;">
                                <span class="detail-label">Observações</span>
                                <p class="detail-value" id="observacoes">-</p>
                            </div>
                        </div>

                        <div class="details-actions three-actions">
                            

                            <button type="button" class="btn-cancel-schedule" id="openCancelSchedule">
                                Cancelar agendamento
                            </button>

                            <a href="index.php?action=scheduling_edit&id=<?= (int)$idAgendamento ?>" class="btn-change">
                                Solicitar alteração
                            </a>

                            <a href="index.php?action=scheduling_create" class="btn-confirmar">
                                Novo agendamento
                            </a>
                        </div>
                    </section>

                </div>
            </section>
        </section>
    </main>



    <div class="logout-modal-overlay" id="cancelScheduleModal" aria-hidden="true">
        <div class="logout-modal">
            <div class="logout-modal-icon cancel-modal-icon">!</div>

            <h2>Deseja realmente cancelar o agendamento?</h2>

            <p>
                Essa ação irá excluir o agendamento da sua conta. Depois de cancelado,
                será necessário criar um novo agendamento caso queira remarcar.
            </p>

            <div class="logout-modal-actions">
                <button type="button" class="btn-cancel-logout" id="closeCancelSchedule">
                    Voltar
                </button>

                <button type="button" class="btn-confirm-logout btn-confirm-cancel-schedule" id="confirmCancelSchedule">
                    Cancelar agendamento
                </button>
            </div>
        </div>
    </div>

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
        const idAgendamento = <?= (int)$idAgendamento ?>;

        const loadingBox = document.getElementById('loading-box');
        const conteudoBox = document.getElementById('agendamento-conteudo');
        const erroBox = document.getElementById('erro-box');
        const erroTexto = document.getElementById('erro-texto');

        function esconderLoading() {
            if (loadingBox) {
                loadingBox.style.display = 'none';
            }
        }

        function mostrarErro(mensagem) {
            esconderLoading();

            conteudoBox.style.display = 'none';
            erroBox.style.display = 'block';
            erroTexto.textContent = mensagem;

            ativarReveals(erroBox);
        }

        function mostrarConteudo() {
            esconderLoading();

            erroBox.style.display = 'none';
            conteudoBox.style.display = 'block';

            ativarReveals(conteudoBox);
        }

        function formatarStatus(status) {
            const statusFormatado = {
                pendente: 'Pendente',
                agendado: 'Agendado',
                cancelado: 'Cancelado',
                concluido: 'Concluído',
                faltou: 'Faltou'
            };

            return statusFormatado[status] || status || '-';
        }

        function formatarDataHoraExtenso(dataHora) {
            if (!dataHora) {
                return '-';
            }

            const data = new Date(dataHora.replace(' ', 'T'));

            if (isNaN(data.getTime())) {
                return dataHora;
            }

            const diasSemana = [
                'Domingo',
                'Segunda-feira',
                'Terça-feira',
                'Quarta-feira',
                'Quinta-feira',
                'Sexta-feira',
                'Sábado'
            ];

            const meses = [
                'janeiro',
                'fevereiro',
                'março',
                'abril',
                'maio',
                'junho',
                'julho',
                'agosto',
                'setembro',
                'outubro',
                'novembro',
                'dezembro'
            ];

            const diaSemana = diasSemana[data.getDay()];
            const dia = String(data.getDate()).padStart(2, '0');
            const mes = meses[data.getMonth()];
            const ano = data.getFullYear();
            const hora = String(data.getHours()).padStart(2, '0');
            const minuto = String(data.getMinutes()).padStart(2, '0');

            return `${diaSemana}, ${dia} de ${mes} de ${ano} às ${hora}:${minuto}`;
        }

        function formatarMoeda(valor) {
            const numero = Number(valor || 0);

            return numero.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function montarTextoServicos(agendamento) {
            if (Array.isArray(agendamento.servicos) && agendamento.servicos.length > 0) {
                const nomes = agendamento.servicos
                    .map(function (servico) {
                        return servico.nome;
                    })
                    .filter(Boolean);

                if (nomes.length > 0) {
                    return nomes.join(' + ');
                }
            }

            if (agendamento.servicos_texto) {
                return agendamento.servicos_texto;
            }

            if (agendamento.servico_nome) {
                return agendamento.servico_nome;
            }

            return 'Serviço não informado';
        }

        async function carregarAgendamento() {
            if (idAgendamento <= 0) {
                mostrarErro('O identificador do agendamento informado é inválido.');
                return;
            }

            try {
                const response = await fetch(`index.php?action=api_scheduling_get_by_id&id=${idAgendamento}`, {
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
                    mostrarErro(
                        resultado.message ||
                        'Você não tem permissão para visualizar este agendamento.'
                    );
                    return;
                }

                const agendamento = resultado.data;

                if (!agendamento) {
                    mostrarErro('Agendamento não encontrado.');
                    return;
                }

                const status = agendamento.status || 'pendente';

                const statusBadge = document.getElementById('status-badge');
                statusBadge.textContent = formatarStatus(status);
                statusBadge.className = `status-badge status-${status}`;

                document.getElementById('servico-texto').textContent = montarTextoServicos(agendamento);
                document.getElementById('barbeiro-nome').textContent = agendamento.barbeiro_nome || '-';
                document.getElementById('data-hora').textContent = formatarDataHoraExtenso(agendamento.data_hora);
                document.getElementById('cliente-nome').textContent = agendamento.cliente_nome || '-';
                document.getElementById('valor-total').textContent = formatarMoeda(
                    agendamento.valor_total || agendamento.preco_total || agendamento.valor || 0
                );

                if (agendamento.descricao && agendamento.descricao.trim() !== '') {
                    document.getElementById('observacoes-box').style.display = 'block';
                    document.getElementById('observacoes').textContent = agendamento.descricao;
                } else {
                    document.getElementById('observacoes-box').style.display = 'none';
                }

                mostrarConteudo();

            } catch (error) {
                console.error(error);
                mostrarErro('Erro ao conectar com a API de agendamento.');
            }
        }




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
        carregarAgendamento();
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



    <script>
        const openCancelSchedule = document.getElementById('openCancelSchedule');
        const cancelScheduleModal = document.getElementById('cancelScheduleModal');
        const closeCancelSchedule = document.getElementById('closeCancelSchedule');
        const confirmCancelSchedule = document.getElementById('confirmCancelSchedule');

        function openCancelScheduleModal() {
            if (cancelScheduleModal) {
                cancelScheduleModal.classList.add('show');
                cancelScheduleModal.setAttribute('aria-hidden', 'false');
            }
        }

        function closeCancelScheduleModal() {
            if (cancelScheduleModal) {
                cancelScheduleModal.classList.remove('show');
                cancelScheduleModal.setAttribute('aria-hidden', 'true');
            }
        }

        if (openCancelSchedule) {
            openCancelSchedule.addEventListener('click', function () {
                openCancelScheduleModal();
            });
        }

        if (closeCancelSchedule) {
            closeCancelSchedule.addEventListener('click', function () {
                closeCancelScheduleModal();
            });
        }

        if (cancelScheduleModal) {
            cancelScheduleModal.addEventListener('click', function (event) {
                if (event.target === cancelScheduleModal) {
                    closeCancelScheduleModal();
                }
            });
        }

        if (confirmCancelSchedule) {
            confirmCancelSchedule.addEventListener('click', async function () {
                confirmCancelSchedule.disabled = true;
                confirmCancelSchedule.textContent = 'CANCELANDO...';

                try {
                    const response = await fetch(`index.php?action=api_scheduling_delete&id=${idAgendamento}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    const responseText = await response.text();

                    let result;

                    try {
                        result = JSON.parse(responseText);
                    } catch (error) {
                        console.error(responseText);
                        throw new Error('A API não retornou um JSON válido.');
                    }

                    if (!response.ok || !result.success) {
                        alert(result.message || 'Não foi possível cancelar o agendamento.');

                        confirmCancelSchedule.disabled = false;
                        confirmCancelSchedule.textContent = 'Cancelar agendamento';

                        return;
                    }

                    confirmCancelSchedule.textContent = 'CANCELADO!';

                    setTimeout(function () {
                        window.location.href = 'index.php?action=scheduling_list';
                    }, 900);

                } catch (error) {
                    console.error(error);

                    alert('Erro ao conectar com a API de agendamento.');

                    confirmCancelSchedule.disabled = false;
                    confirmCancelSchedule.textContent = 'Cancelar agendamento';
                }
            });
        }
    </script>

</body>
</html>