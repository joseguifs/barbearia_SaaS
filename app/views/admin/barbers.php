<?php
if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$adminNome = $_SESSION['admin_nome'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Barbeiros - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin-manage.css?v=3">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=admin_home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=admin_home">Home Admin</a>
        <a href="index.php?action=admin_schedulings">Agendamentos</a>
        <a href="index.php?action=admin_clients">Usuários</a>
        <a href="index.php?action=admin_barbers" class="active">Barbeiros</a>
        <a href="index.php?action=admin_services">Serviços</a>

        <form
            action="index.php?action=admin_logout"
            method="POST"
            class="logout-form"
            onsubmit="return confirm('Deseja sair da conta de administrador?');"
        >
            <button type="submit" class="btn-logout">
                Sair
            </button>
        </form>
    </nav>
</header>

<main class="page">
    <section class="admin-container">

        <aside class="admin-side">
            <span class="side-tag">Barbeiros</span>

            <h1>Gerenciar barbeiros</h1>

            <p>
                Cadastre, edite e remova barbeiros da plataforma. Cada barbeiro terá
                e-mail e senha para acessar o painel próprio de gerenciamento de agendamentos.
            </p>

            <div class="manager-box">
                <div class="manager-avatar">
                    <?= e(strtoupper(substr($adminNome, 0, 1))) ?>
                </div>

                <div>
                    <span>Admin logado</span>
                    <strong><?= e($adminNome) ?></strong>
                </div>
            </div>
        </aside>

        <section class="admin-content">
            <div class="content-header">
                <span>Controle de barbeiros</span>
                <h2>Barbeiros cadastrados</h2>
                <p>
                    Gerencie os profissionais disponíveis no sistema, seus serviços e a senha de acesso.
                </p>
            </div>

            <div id="message-box" class="alert" style="display: none;"></div>

            <form class="filter-card" onsubmit="return false;">
                <input
                    type="text"
                    id="search-input"
                    placeholder="Buscar barbeiro por nome ou e-mail..."
                >

                <button type="button" class="btn-main" id="reload-button">
                    Atualizar
                </button>
            </form>

            <details class="create-card" open>
                <summary>Novo barbeiro</summary>

                <form id="barber-form" class="form-grid">
                    <input
                        type="text"
                        id="barber-name"
                        placeholder="Nome do barbeiro"
                        autocomplete="off"
                        required
                    >

                    <input
                        type="email"
                        id="barber-email"
                        placeholder="E-mail de acesso"
                        autocomplete="off"
                        required
                    >

                    <input
                        type="password"
                        id="barber-password"
                        placeholder="Senha de acesso"
                        autocomplete="new-password"
                        required
                    >

                    <input
                        type="text"
                        id="barber-services"
                        placeholder="IDs dos serviços. Ex: 1,2,3"
                        autocomplete="off"
                    >

                    <button type="submit" class="btn-main">
                        Cadastrar barbeiro
                    </button>

                    <button type="button" class="btn-secondary-simple" id="clear-button">
                        Limpar
                    </button>
                </form>
            </details>

            <div id="loading-state" class="empty-card">
                Carregando barbeiros...
            </div>

            <div id="barbers-grid" class="list-grid"></div>
        </section>

    </section>
</main>

<script>
    const API_BASE = 'index.php';

    const barberForm = document.getElementById('barber-form');
    const barberNameInput = document.getElementById('barber-name');
    const barberEmailInput = document.getElementById('barber-email');
    const barberPasswordInput = document.getElementById('barber-password');
    const barberServicesInput = document.getElementById('barber-services');

    const clearButton = document.getElementById('clear-button');
    const reloadButton = document.getElementById('reload-button');
    const searchInput = document.getElementById('search-input');

    const barbersGrid = document.getElementById('barbers-grid');
    const loadingState = document.getElementById('loading-state');
    const messageBox = document.getElementById('message-box');

    let barbers = [];

    function showMessage(message, type = 'success') {
        messageBox.style.display = 'block';
        messageBox.textContent = message;

        if (type === 'error') {
            messageBox.style.color = '#ffd3d3';
            messageBox.style.borderColor = 'rgba(244, 67, 54, 0.34)';
            messageBox.style.background = 'rgba(183, 28, 28, 0.18)';
            return;
        }

        messageBox.style.color = '#d7ffd4';
        messageBox.style.borderColor = 'rgba(76, 175, 80, 0.38)';
        messageBox.style.background = 'rgba(46, 125, 50, 0.18)';
    }

    function hideMessage() {
        messageBox.style.display = 'none';
        messageBox.textContent = '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function parseServiceIds(value) {
        if (!value || !value.trim()) {
            return [];
        }

        return value
            .split(',')
            .map(item => item.trim())
            .filter(item => item !== '')
            .map(item => Number(item))
            .filter(item => Number.isInteger(item) && item > 0);
    }

    function getServiceIdsFromBarber(barber) {
        if (!Array.isArray(barber.servicos)) {
            return '';
        }

        return barber.servicos
            .map(service => service.id_servico)
            .filter(Boolean)
            .join(',');
    }

    function getServiceNames(barber) {
        if (!Array.isArray(barber.servicos) || barber.servicos.length === 0) {
            return 'Sem serviços cadastrados';
        }

        return barber.servicos
            .map(service => service.nome)
            .filter(Boolean)
            .join(', ');
    }

    function renderBarbers(list) {
        loadingState.style.display = 'none';
        barbersGrid.innerHTML = '';

        if (list.length === 0) {
            barbersGrid.innerHTML = `
                <div class="empty-card">
                    Nenhum barbeiro encontrado.
                </div>
            `;
            return;
        }

        list.forEach((barber) => {
            const article = document.createElement('article');
            article.className = 'manage-card';

            article.innerHTML = `
                <div class="card-title">
                    <div>
                        <span>#${escapeHtml(barber.id_barbeiro)}</span>
                        <h3>${escapeHtml(barber.nome)}</h3>
                        <p>${escapeHtml(barber.email || 'E-mail não cadastrado')}</p>
                    </div>

                    <strong class="status-pill">Barbeiro</strong>
                </div>

                <p class="card-description">
                    <strong>Serviços:</strong> ${escapeHtml(getServiceNames(barber))}
                </p>

                <form class="form-grid edit-form" data-id="${escapeHtml(barber.id_barbeiro)}">
                    <input
                        type="text"
                        name="nome"
                        value="${escapeHtml(barber.nome)}"
                        placeholder="Nome do barbeiro"
                        required
                    >

                    <input
                        type="email"
                        name="email"
                        value="${escapeHtml(barber.email || '')}"
                        placeholder="E-mail do barbeiro"
                    >

                    <input
                        type="password"
                        name="senha"
                        placeholder="Nova senha opcional"
                        autocomplete="new-password"
                    >

                    <input
                        type="text"
                        name="servicos"
                        value="${escapeHtml(getServiceIdsFromBarber(barber))}"
                        placeholder="IDs dos serviços. Ex: 1,2,3"
                    >

                    <button type="submit" class="btn-main">
                        Salvar alterações
                    </button>
                </form>

                <form
                    class="delete-form"
                    data-id="${escapeHtml(barber.id_barbeiro)}"
                    data-name="${escapeHtml(barber.nome)}"
                >
                    <button type="submit" class="btn-danger">
                        Excluir barbeiro
                    </button>
                </form>
            `;

            barbersGrid.appendChild(article);
        });
    }

    function filterBarbers() {
        const term = searchInput.value.trim().toLowerCase();

        if (!term) {
            renderBarbers(barbers);
            return;
        }

        const filtered = barbers.filter(barber => {
            const nome = String(barber.nome || '').toLowerCase();
            const email = String(barber.email || '').toLowerCase();

            return nome.includes(term) || email.includes(term);
        });

        renderBarbers(filtered);
    }

    async function readJsonResponse(response) {
        try {
            return await response.json();
        } catch (error) {
            return {
                success: false,
                message: 'A API retornou uma resposta inválida.'
            };
        }
    }

    async function loadBarbers() {
        hideMessage();
        loadingState.style.display = 'block';
        loadingState.textContent = 'Carregando barbeiros...';
        barbersGrid.innerHTML = '';

        try {
            const response = await fetch(`${API_BASE}?action=api_barber_index`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await readJsonResponse(response);

            if (!response.ok || !result.success) {
                loadingState.style.display = 'none';
                showMessage(result.message || 'Erro ao carregar barbeiros.', 'error');
                return;
            }

            barbers = result.data || [];
            filterBarbers();

        } catch (error) {
            loadingState.style.display = 'none';
            showMessage('Erro ao comunicar com a API.', 'error');
        }
    }

    async function saveNewBarber(event) {
        event.preventDefault();

        const nome = barberNameInput.value.trim();
        const email = barberEmailInput.value.trim();
        const senha = barberPasswordInput.value.trim();
        const servicos = parseServiceIds(barberServicesInput.value);

        if (!nome) {
            showMessage('Informe o nome do barbeiro.', 'error');
            barberNameInput.focus();
            return;
        }

        if (!email) {
            showMessage('Informe o e-mail do barbeiro.', 'error');
            barberEmailInput.focus();
            return;
        }

        if (!senha) {
            showMessage('Informe a senha de acesso do barbeiro.', 'error');
            barberPasswordInput.focus();
            return;
        }

        if (senha.length < 5) {
            showMessage('A senha deve ter pelo menos 5 caracteres.', 'error');
            barberPasswordInput.focus();
            return;
        }

        try {
            const response = await fetch(`${API_BASE}?action=api_barber_store`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nome,
                    email,
                    senha,
                    servicos
                })
            });

            const result = await readJsonResponse(response);

            if (!response.ok || !result.success) {
                showMessage(result.message || 'Não foi possível cadastrar o barbeiro.', 'error');
                return;
            }

            showMessage(result.message || 'Barbeiro cadastrado com sucesso.');
            clearCreateForm();
            await loadBarbers();

        } catch (error) {
            showMessage('Erro ao cadastrar barbeiro.', 'error');
        }
    }

    async function saveEditBarber(form) {
        const id = form.dataset.id;

        const nomeInput = form.querySelector('input[name="nome"]');
        const emailInput = form.querySelector('input[name="email"]');
        const senhaInput = form.querySelector('input[name="senha"]');
        const servicosInput = form.querySelector('input[name="servicos"]');
        const button = form.querySelector('button[type="submit"]');

        const nome = nomeInput ? nomeInput.value.trim() : '';
        const email = emailInput ? emailInput.value.trim() : '';
        const senha = senhaInput ? senhaInput.value.trim() : '';
        const servicos = servicosInput ? parseServiceIds(servicosInput.value) : [];

        if (!id) {
            showMessage('ID do barbeiro não encontrado.', 'error');
            return;
        }

        if (!nome) {
            showMessage('Informe o nome do barbeiro.', 'error');
            if (nomeInput) nomeInput.focus();
            return;
        }

        if (senha !== '' && senha.length < 5) {
            showMessage('A nova senha deve ter pelo menos 5 caracteres.', 'error');
            if (senhaInput) senhaInput.focus();
            return;
        }

        const payload = {
            id: Number(id),
            id_barbeiro: Number(id),
            nome: nome,
            email: email,
            servicos: servicos
        };

        if (senha !== '') {
            payload.senha = senha;
        }

        if (button) {
            button.disabled = true;
            button.textContent = 'Salvando...';
        }

        try {
            showMessage('Salvando alterações...');

            const response = await fetch(`${API_BASE}?action=api_barber_update&id=${encodeURIComponent(id)}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await readJsonResponse(response);

            if (!response.ok || !result.success) {
                showMessage(result.message || 'Não foi possível atualizar o barbeiro.', 'error');

                if (button) {
                    button.disabled = false;
                    button.textContent = 'Salvar alterações';
                }

                return;
            }

            showMessage(result.message || 'Barbeiro atualizado com sucesso.');
            await loadBarbers();

        } catch (error) {
            showMessage('Erro ao atualizar barbeiro.', 'error');

            if (button) {
                button.disabled = false;
                button.textContent = 'Salvar alterações';
            }
        }
    }

    async function deleteBarber(form) {
        const id = form.dataset.id;
        const name = form.dataset.name || 'este barbeiro';
        const button = form.querySelector('button[type="submit"]');

        if (!id) {
            showMessage('ID do barbeiro não encontrado.', 'error');
            return;
        }

        if (!confirm(`Deseja realmente excluir ${name}?`)) {
            return;
        }

        if (button) {
            button.disabled = true;
            button.textContent = 'Excluindo...';
        }

        try {
            const response = await fetch(`${API_BASE}?action=api_barber_delete&id=${encodeURIComponent(id)}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await readJsonResponse(response);

            if (!response.ok || !result.success) {
                showMessage(result.message || 'Não foi possível excluir o barbeiro.', 'error');

                if (button) {
                    button.disabled = false;
                    button.textContent = 'Excluir barbeiro';
                }

                return;
            }

            showMessage(result.message || 'Barbeiro excluído com sucesso.');
            await loadBarbers();

        } catch (error) {
            showMessage('Erro ao excluir barbeiro.', 'error');

            if (button) {
                button.disabled = false;
                button.textContent = 'Excluir barbeiro';
            }
        }
    }

    function clearCreateForm() {
        barberNameInput.value = '';
        barberEmailInput.value = '';
        barberPasswordInput.value = '';
        barberServicesInput.value = '';
    }

    barberForm.addEventListener('submit', saveNewBarber);

    clearButton.addEventListener('click', function () {
        clearCreateForm();
        hideMessage();
    });

    reloadButton.addEventListener('click', function () {
        loadBarbers();
    });

    searchInput.addEventListener('input', filterBarbers);

    barbersGrid.addEventListener('submit', function (event) {
        const editForm = event.target.closest('.edit-form');
        const deleteForm = event.target.closest('.delete-form');

        if (editForm) {
            event.preventDefault();
            saveEditBarber(editForm);
            return;
        }

        if (deleteForm) {
            event.preventDefault();
            deleteBarber(deleteForm);
        }
    });

    loadBarbers();
</script>

</body>
</html>