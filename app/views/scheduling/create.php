<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$formData = $formData ?? [];

$selectedBarbeiro = $formData['barbeiro_id'] ?? '';
$selectedServicos = $formData['servicos'] ?? [];
$selectedData = $formData['data_agendamento'] ?? '';
$selectedHora = $formData['hora_agendamento'] ?? '';
$selectedDescricao = $formData['descricao'] ?? '';

if (!is_array($selectedServicos)) {
    $selectedServicos = [];
}

$success = $success ?? (isset($_GET['success']) && $_GET['success'] === '1');

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Agendamento - BarberTime</title>
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css">
</head>
<body>

    <header class="topbar reveal-item">
        <a href="index.php?action=home" class="logo">BARBERTIME</a>

        <nav class="navbar">
            <a href="index.php?action=home">Início</a>
            <a href="index.php?action=scheduling_create" class="active">Agendar</a>
            <a href="index.php?action=scheduling_list">Meus Agendamentos</a>
            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </nav>
    </header>

    <main class="page">

        <section class="schedule-container">

            <aside class="schedule-side reveal-item">
                <span class="side-tag">Agendamento online</span>

                <h1>Novo agendamento</h1>

                <p>
                    Escolha o barbeiro, selecione os serviços desejados e defina
                    a melhor data e horário para seu atendimento.
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
                    <strong>Importante</strong>
                    <p>
                        O agendamento será vinculado automaticamente ao cliente
                        logado na sessão atual.
                    </p>
                </div>
            </aside>

            <section class="schedule-content reveal-item">

                <div class="content-header">
                    <span>BarberTime</span>
                    <h2>Preencha os dados do atendimento</h2>
                    <p>Todos os campos obrigatórios precisam ser preenchidos.</p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert success-message">
                        Agendamento cadastrado com sucesso.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert error-message">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                <?php endif; ?>

                <div id="formMessage" class="alert" style="display: none;"></div>

                <form action="index.php?action=api_scheduling_store" method="POST" class="schedule-form" id="scheduleForm">

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">01</span>

                            <div>
                                <h3>Selecione o barbeiro</h3>
                                <p>Escolha o profissional desejado para o atendimento.</p>
                            </div>
                        </div>

                        <div class="barbeiros-lista">
                            <?php if (!empty($barbeiros)): ?>
                                <?php foreach ($barbeiros as $barbeiro): ?>
                                    <label class="barbeiro-item">
                                        <input
                                            type="radio"
                                            name="barbeiro_id"
                                            value="<?= $barbeiro['id_barbeiro'] ?>"
                                            <?= (string)$selectedBarbeiro === (string)$barbeiro['id_barbeiro'] ? 'checked' : '' ?>
                                            required
                                        >

                                        <span class="barbeiro-avatar">
                                            <?= htmlspecialchars(function_exists('mb_substr')
                                                ? mb_strtoupper(mb_substr($barbeiro['nome'], 0, 1, 'UTF-8'), 'UTF-8')
                                                : strtoupper(substr($barbeiro['nome'], 0, 1))) ?>
                                        </span>

                                        <span class="barbeiro-info">
                                            <strong><?= htmlspecialchars($barbeiro['nome']) ?></strong>
                                        </span>

                                        <span class="checkmark">✔</span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="empty-message">Nenhum barbeiro disponível.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">02</span>

                            <div>
                                <h3>Selecione os serviços</h3>
                                <p>Você pode marcar um ou mais serviços.</p>
                            </div>
                        </div>

                        <div class="servicos-grid">
                            <?php if (!empty($servicos)): ?>
                                <?php foreach ($servicos as $servico): ?>
                                    <label class="servico-item">
                                        <input
                                            type="checkbox"
                                            name="servicos[]"
                                            value="<?= $servico['id_servico'] ?>"
                                            <?= in_array((int)$servico['id_servico'], array_map('intval', $selectedServicos), true) ? 'checked' : '' ?>
                                        >

                                        <span class="servico-content">
                                            <strong><?= htmlspecialchars($servico['nome']) ?></strong>

                                            <small>
                                                R$ <?= number_format((float)$servico['preco'], 2, ',', '.') ?>

                                                <?php if (!empty($servico['duracao'])): ?>
                                                    · <?= (int)$servico['duracao'] ?> min
                                                <?php endif; ?>
                                            </small>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="empty-message">Nenhum serviço disponível.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">03</span>

                            <div>
                                <h3>Data e horário</h3>
                                <p>Defina quando deseja realizar o atendimento.</p>
                            </div>
                        </div>

                        <div class="datetime-grid">
                            <div class="field-group">
                                <label for="data_agendamento">Data</label>

                                <input
                                    type="date"
                                    id="data_agendamento"
                                    name="data_agendamento"
                                    value="<?= htmlspecialchars($selectedData) ?>"
                                    min="<?= $dataMinima ?>"
                                    required
                                >
                            </div>

                            <div class="field-group">
                                <label for="hora_agendamento">Horário</label>

                                <select id="hora_agendamento" name="hora_agendamento" required disabled>
                                    <option value="">Selecione barbeiro, serviço(s) e data</option>
                                </select>

                                <small id="horarioFeedback" class="field-help"></small>
                            </div>
                        </div>
                    </section>

                    <section class="card reveal-item micro-lift">
                        <div class="card-header">
                            <span class="step-number">04</span>

                            <div>
                                <h3>Observações</h3>
                                <p>Informe detalhes adicionais caso necessário.</p>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="descricao">Descrição</label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="5"
                                placeholder="Digite aqui alguma observação importante sobre seu atendimento..."
                            ><?= htmlspecialchars($selectedDescricao) ?></textarea>
                        </div>
                    </section>

                    <div class="form-actions">
                        <a href="index.php?action=home" class="btn-secondary">Voltar</a>

                        <button type="submit" class="btn-confirmar" id="submitButton">
                            Confirmar agendamento
                        </button>
                    </div>
                </form>
            </section>
        </section>

        
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

    </main>

    <script>
        const scheduleForm = document.getElementById('scheduleForm');
        const submitButton = document.getElementById('submitButton');
        const formMessage = document.getElementById('formMessage');

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
                ? 'AGENDANDO...'
                : 'Confirmar agendamento';
        }

        if (scheduleForm && submitButton) {
            scheduleForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                clearMessage();

                const barbeiroSelecionado = document.querySelector('input[name="barbeiro_id"]:checked');
                const servicosSelecionados = document.querySelectorAll('input[name="servicos[]"]:checked');
                const data = document.getElementById('data_agendamento').value;
                const hora = document.getElementById('hora_agendamento').value;
                const descricao = document.getElementById('descricao').value.trim();

                if (!barbeiroSelecionado || servicosSelecionados.length === 0 || !data || !hora) {
                    showMessage('Preencha barbeiro, serviço(s), data e horário.', 'error');
                    return;
                }

                const servicos = Array.from(servicosSelecionados).map(function (servico) {
                    return parseInt(servico.value);
                });

                setLoading(true);

                try {
                    const response = await fetch('index.php?action=api_scheduling_store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            barbeiro_id: parseInt(barbeiroSelecionado.value),
                            servicos: servicos,
                            data_agendamento: data,
                            hora_agendamento: hora,
                            descricao: descricao
                        })
                    });

                    const responseText = await response.text();
                    let result = null;

                    try {
                        result = JSON.parse(responseText);
                    } catch (error) {
                        throw new Error('A API não retornou um JSON válido.');
                    }

                    if (!response.ok || !result.success) {
                        showMessage(result.message || 'Não foi possível criar o agendamento.', 'error');
                        setLoading(false);
                        return;
                    }

                    showMessage(result.message || 'Agendamento criado com sucesso.', 'success');

                    submitButton.textContent = 'AGENDADO!';

                    setTimeout(function () {
                        window.location.href = 'index.php?action=scheduling_create&success=1';
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

    <script>
        const dataInput = document.getElementById('data_agendamento');
        const horaSelect = document.getElementById('hora_agendamento');
        const horarioFeedback = document.getElementById('horarioFeedback');

        function getBarbeiroSelecionado() {
            return document.querySelector('input[name="barbeiro_id"]:checked');
        }

        function getServicosSelecionados() {
            return Array.from(document.querySelectorAll('input[name="servicos[]"]:checked'))
                .map(function (item) {
                    return item.value;
                });
        }

        function resetHorarios(mensagem) {
            horaSelect.innerHTML = `<option value="">${mensagem}</option>`;
            horaSelect.disabled = true;

            if (horarioFeedback) {
                horarioFeedback.textContent = mensagem;
            }
        }

        async function carregarHorariosDisponiveis() {
            const barbeiro = getBarbeiroSelecionado();
            const servicos = getServicosSelecionados();
            const data = dataInput.value;

            if (!barbeiro || servicos.length === 0 || !data) {
                resetHorarios('Selecione barbeiro, serviço(s) e data');
                return;
            }

            horaSelect.disabled = true;
            horaSelect.innerHTML = '<option value="">Carregando horários...</option>';

            if (horarioFeedback) {
                horarioFeedback.textContent = 'Buscando horários disponíveis...';
            }

            const params = new URLSearchParams();

            params.append('action', 'api_scheduling_available_times');
            params.append('id_barbeiro', barbeiro.value);
            params.append('data_agendamento', data);

            servicos.forEach(function (idServico) {
                params.append('servicos[]', idServico);
            });

            try {
                const response = await fetch(`index.php?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    resetHorarios(result.message || 'Não foi possível carregar os horários.');
                    return;
                }

                const horarios = result.data || [];

                if (horarios.length === 0) {
                    resetHorarios('Nenhum horário disponível para essa data');
                    return;
                }

                horaSelect.innerHTML = '<option value="">Selecione um horário</option>';

                horarios.forEach(function (horario) {
                    const option = document.createElement('option');
                    option.value = horario;
                    option.textContent = horario;
                    horaSelect.appendChild(option);
                });

                horaSelect.disabled = false;

                if (horarioFeedback) {
                    horarioFeedback.textContent = 'Horários carregados com base na duração dos serviços.';
                }

            } catch (error) {
                console.error(error);
                resetHorarios('Erro ao buscar horários disponíveis.');
            }
        }

        document.querySelectorAll('input[name="barbeiro_id"]').forEach(function (input) {
            input.addEventListener('change', carregarHorariosDisponiveis);
        });

        document.querySelectorAll('input[name="servicos[]"]').forEach(function (input) {
            input.addEventListener('change', carregarHorariosDisponiveis);
        });

        if (dataInput) {
            dataInput.addEventListener('change', carregarHorariosDisponiveis);
        }

        carregarHorariosDisponiveis();
    </script>

</body>
</html>