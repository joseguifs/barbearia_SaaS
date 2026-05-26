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

$agendamento = $agendamento ?? [];

$horariosDisponiveis = $horariosDisponiveis ?? [
    '09:00',
    '09:30',
    '10:00',
    '10:30',
    '11:00',
    '14:00',
    '14:30',
    '15:00',
    '15:30',
    '16:00'
];

$idAgendamento = $agendamento['id_agendamento'] ?? ($_GET['id'] ?? '');

$dataSelecionada = $dataSelecionada ?? '';

if (empty($dataSelecionada) && !empty($agendamento['data_hora'])) {
    $dataSelecionada = date('Y-m-d', strtotime($agendamento['data_hora']));
}

if (empty($dataSelecionada)) {
    $dataSelecionada = date('Y-m-d');
}

$horaSelecionada = $horaSelecionada ?? '';

if (empty($horaSelecionada) && !empty($agendamento['data_hora'])) {
    $horaSelecionada = date('H:i', strtotime($agendamento['data_hora']));
}

$clienteNomeSessao = $_SESSION['cliente_nome'] ?? $_SESSION['nome'] ?? 'Cliente';

$clienteInicial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($clienteNomeSessao, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($clienteNomeSessao, 0, 1));

$dataMinima = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Agendamento - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css">

    <style>
        .current-summary {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .summary-line {
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.045);
        }

        .summary-line span {
            display: block;
            color: var(--primary);
            font-size: 0.76rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 6px;
        }

        .summary-line strong {
            color: #fff;
            font-size: 0.98rem;
            line-height: 1.5;
        }

        .date-display {
            font-family: Arial, Helvetica, sans-serif;
        }

        .times-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(100px, 1fr));
            gap: 12px;
        }

        .time-option {
            cursor: pointer;
        }

        .time-option input {
            display: none;
        }

        .time-option span {
            min-height: 48px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--input-bg);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: 0.25s ease;
        }

        .time-option:hover span {
            border-color: rgba(197, 157, 95, 0.40);
            background: var(--input-bg-hover);
            transform: translateY(-2px);
        }

        .time-option input:checked + span {
            border-color: rgba(197, 157, 95, 0.70);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.22);
        }

        .reveal-item {
            opacity: 0;
            transform: translateY(28px);
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
            .times-grid {
                grid-template-columns: repeat(2, minmax(100px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .times-grid {
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
            <a href="index.php?action=scheduling_list">Meus Agendamentos</a>
            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </nav>
    </header>

    <main class="page">

        <section class="schedule-container">

            <aside class="schedule-side reveal-item">
                <span class="side-tag">Alteração de horário</span>

                <h1>Alterar agendamento</h1>

                <p>
                    Ajuste a data e o horário do seu atendimento. A alteração será
                    enviada para a API de agendamentos.
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
                    <strong>Importante</strong>
                    <p>
                        Você só pode alterar agendamentos vinculados à sua conta.
                        A API valida o cliente logado antes de salvar.
                    </p>
                </div>
            </aside>

            <section class="schedule-content reveal-item">
                <div class="content-header">
                    <span>BarberTime</span>
                    <h2>Atualize seu atendimento</h2>
                    <p>Confira o agendamento atual e selecione uma nova data e horário.</p>
                </div>

                <div id="formMessage" class="alert" style="display: none;"></div>

                <form action="index.php?action=api_scheduling_update&id=<?= e($idAgendamento) ?>" method="POST" class="schedule-form" id="updateScheduleForm">

                    <input type="hidden" id="id_agendamento" name="id_agendamento" value="<?= e($idAgendamento) ?>">

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">01</span>

                            <div>
                                <h3>Agendamento atual</h3>
                                <p>Essas são as informações atuais do seu atendimento.</p>
                            </div>
                        </div>

                        <div class="current-summary">
                            <div class="summary-line">
                                <span>Barbeiro</span>
                                <strong><?= e($agendamento['barbeiro_nome'] ?? 'Barbeiro não informado') ?></strong>
                            </div>

                            <div class="summary-line">
                                <span>Serviço(s)</span>
                                <strong><?= e($agendamento['servicos_texto'] ?? 'Serviço não informado') ?></strong>
                            </div>

                            <div class="summary-line">
                                <span>Data atual</span>
                                <strong class="date-display">
                                    <?= e($agendamento['data_formatada'] ?? ($agendamento['data_hora'] ?? 'Data não informada')) ?>
                                </strong>
                            </div>

                            <div class="summary-line">
                                <span>Horário atual</span>
                                <strong><?= e($agendamento['hora_formatada'] ?? $horaSelecionada) ?></strong>
                            </div>
                        </div>
                    </section>

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">02</span>

                            <div>
                                <h3>Selecione a nova data</h3>
                                <p>Escolha a nova data para o seu atendimento.</p>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="data_agendamento">Data</label>

                            <input
                                type="date"
                                id="data_agendamento"
                                name="data_agendamento"
                                value="<?= e($dataSelecionada) ?>"
                                min="<?= e($dataMinima) ?>"
                                required
                            >
                        </div>
                    </section>

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">03</span>

                            <div>
                                <h3>Selecione o novo horário</h3>
                                <p>Escolha entre os horários disponíveis para atendimento.</p>
                            </div>
                        </div>

                        <div class="times-grid">
                            <?php foreach ($horariosDisponiveis as $horario): ?>
                                <label class="time-option micro-lift">
                                    <input
                                        type="radio"
                                        name="hora_agendamento"
                                        value="<?= e($horario) ?>"
                                        <?= $horaSelecionada === $horario ? 'checked' : '' ?>
                                        required
                                    >

                                    <span><?= e($horario) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <div class="form-actions">
                        <a href="index.php?action=scheduling_get&id=<?= e($idAgendamento) ?>" class="btn-secondary">Cancelar</a>

                        <button type="submit" class="btn-confirmar" id="submitButton">
                            Confirmar alteração
                        </button>
                    </div>
                </form>
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
        const updateScheduleForm = document.getElementById('updateScheduleForm');
        const submitButton = document.getElementById('submitButton');
        const formMessage = document.getElementById('formMessage');
        const idAgendamento = document.getElementById('id_agendamento').value;

        function showMessage(message, type) {
            if (!formMessage) return;

            formMessage.textContent = message;
            formMessage.style.display = 'block';

            formMessage.classList.remove('success-message', 'error-message');

            if (type === 'success') {
                formMessage.classList.add('success-message');
            } else {
                formMessage.classList.add('error-message');
            }

            formMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        function clearMessage() {
            if (!formMessage) return;

            formMessage.textContent = '';
            formMessage.style.display = 'none';
            formMessage.classList.remove('success-message', 'error-message');
        }

        function setLoading(isLoading) {
            if (!submitButton) return;

            submitButton.disabled = isLoading;
            submitButton.textContent = isLoading
                ? 'SALVANDO...'
                : 'Confirmar alteração';
        }

        if (updateScheduleForm && submitButton) {
            updateScheduleForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                clearMessage();

                const data = document.getElementById('data_agendamento').value;
                const horarioSelecionado = document.querySelector('input[name="hora_agendamento"]:checked');

                if (!data || !horarioSelecionado) {
                    showMessage('Selecione a nova data e o novo horário.', 'error');
                    return;
                }

                const hora = horarioSelecionado.value;
                const dataHora = `${data} ${hora}:00`;

                setLoading(true);

                try {
                    const response = await fetch(`index.php?action=api_scheduling_update&id=${idAgendamento}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            data_hora: dataHora
                        })
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
                        showMessage(result.message || 'Não foi possível alterar o agendamento.', 'error');
                        setLoading(false);
                        return;
                    }

                    showMessage(result.message || 'Agendamento atualizado com sucesso.', 'success');

                    submitButton.textContent = 'ALTERADO!';

                    setTimeout(function () {
                        window.location.href = `index.php?action=scheduling_get&id=${idAgendamento}`;
                    }, 900);

                } catch (error) {
                    console.error(error);

                    showMessage('Erro ao conectar com a API de agendamento.', 'error');
                    setLoading(false);
                }
            });
        }
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
    </script>
</body>
</html>