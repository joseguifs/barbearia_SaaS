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
    <title>Gerenciar Serviços - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Mesmo CSS usado nas telas de usuários, agendamentos e barbeiros -->
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin-manage.css">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=admin_home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=admin_home">Home Admin</a>
        <a href="index.php?action=admin_schedulings">Agendamentos</a>
        <a href="index.php?action=admin_clients">Usuários</a>
        <a href="index.php?action=admin_barbers">Barbeiros</a>
        <a href="index.php?action=admin_services" class="active">Serviços</a>

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
            <span class="side-tag">Serviços</span>

            <h1>Gerenciar serviços</h1>

            <p>
                Cadastre, edite e remova os serviços disponíveis para agendamento.
                Cada serviço possui nome, descrição, preço e duração aproximada.
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
                <span>Controle de serviços</span>
                <h2>Serviços cadastrados</h2>
                <p>
                    Gerencie os serviços oferecidos pela barbearia e mantenha o catálogo sempre atualizado.
                </p>
            </div>

            <div id="message-box" class="alert" style="display: none;"></div>

            <form class="filter-card" onsubmit="return false;">
                <input
                    type="text"
                    id="search-input"
                    placeholder="Buscar serviço por nome ou descrição..."
                >

                <button type="button" class="btn-main" id="reload-button">
                    Atualizar
                </button>
            </form>

            <details class="create-card" open>
                <summary>Novo serviço</summary>

                <form id="service-form" class="form-grid">
                    <input
                        type="text"
                        id="service-name"
                        placeholder="Nome do serviço"
                        autocomplete="off"
                        required
                    >

                    <input
                        type="number"
                        id="service-price"
                        placeholder="Preço. Ex: 45.00"
                        step="0.01"
                        min="0"
                        required
                    >

                    <input
                        type="number"
                        id="service-duration"
                        placeholder="Duração em minutos"
                        min="1"
                        required
                    >

                    <textarea
                        id="service-description"
                        placeholder="Descrição do serviço"
                        class="full"
                    ></textarea>

                    <button type="submit" class="btn-main">
                        Cadastrar serviço
                    </button>

                    <button type="button" class="btn-secondary-simple" id="clear-button">
                        Limpar
                    </button>
                </form>
            </details>

            <div id="loading-state" class="empty-card">
                Carregando serviços...
            </div>

            <div id="services-grid" class="list-grid"></div>
        </section>

    </section>
</main>

<script>
    const API_BASE = 'index.php';

    const serviceForm = document.getElementById('service-form');
    const serviceNameInput = document.getElementById('service-name');
    const serviceDescriptionInput = document.getElementById('service-description');
    const servicePriceInput = document.getElementById('service-price');
    const serviceDurationInput = document.getElementById('service-duration');

    const clearButton = document.getElementById('clear-button');
    const reloadButton = document.getElementById('reload-button');
    const searchInput = document.getElementById('search-input');

    const servicesGrid = document.getElementById('services-grid');
    const loadingState = document.getElementById('loading-state');
    const messageBox = document.getElementById('message-box');

    let services = [];

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

    function formatMoney(value) {
        return Number(value || 0).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function renderServices(list) {
        loadingState.style.display = 'none';
        servicesGrid.innerHTML = '';

        if (list.length === 0) {
            servicesGrid.innerHTML = `
                <div class="empty-card">
                    Nenhum serviço encontrado.
                </div>
            `;
            return;
        }

        list.forEach((service) => {
            const article = document.createElement('article');
            article.className = 'manage-card';

            article.innerHTML = `
                <div class="card-title">
                    <div>
                        <span>#${escapeHtml(service.id_servico)}</span>
                        <h3>${escapeHtml(service.nome)}</h3>
                        <p>
                            ${formatMoney(service.preco)}
                            •
                            ${Number(service.duracao || 0)} minutos
                        </p>
                    </div>

                    <strong class="status-pill">Serviço</strong>
                </div>

                <p class="card-description">
                    ${service.descricao
                        ? escapeHtml(service.descricao)
                        : 'Serviço sem descrição cadastrada.'}
                </p>

                <form class="form-grid edit-form" data-id="${escapeHtml(service.id_servico)}">
                    <input
                        type="text"
                        name="nome"
                        value="${escapeHtml(service.nome)}"
                        placeholder="Nome do serviço"
                        required
                    >

                    <input
                        type="number"
                        name="preco"
                        value="${escapeHtml(service.preco)}"
                        placeholder="Preço"
                        step="0.01"
                        min="0"
                        required
                    >

                    <input
                        type="number"
                        name="duracao"
                        value="${escapeHtml(service.duracao)}"
                        placeholder="Duração em minutos"
                        min="1"
                        required
                    >

                    <textarea
                        name="descricao"
                        placeholder="Descrição do serviço"
                        class="full"
                    >${escapeHtml(service.descricao || '')}</textarea>

                    <button type="submit" class="btn-main">
                        Salvar alterações
                    </button>
                </form>

                <form
                    class="delete-form"
                    data-id="${escapeHtml(service.id_servico)}"
                    data-name="${escapeHtml(service.nome)}"
                >
                    <button type="submit" class="btn-danger">
                        Excluir serviço
                    </button>
                </form>
            `;

            servicesGrid.appendChild(article);
        });
    }

    function filterServices() {
        const term = searchInput.value.trim().toLowerCase();

        if (!term) {
            renderServices(services);
            return;
        }

        const filtered = services.filter(service => {
            const nome = String(service.nome || '').toLowerCase();
            const descricao = String(service.descricao || '').toLowerCase();

            return nome.includes(term) || descricao.includes(term);
        });

        renderServices(filtered);
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

    async function loadServices() {
        hideMessage();

        loadingState.style.display = 'block';
        loadingState.textContent = 'Carregando serviços...';
        servicesGrid.innerHTML = '';

        try {
            const response = await fetch(`${API_BASE}?action=api_service_index`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await readJsonResponse(response);

            if (!result.success) {
                loadingState.style.display = 'none';
                showMessage(result.message || 'Erro ao carregar serviços.', 'error');
                return;
            }

            services = result.data || [];
            filterServices();

        } catch (error) {
            loadingState.style.display = 'none';
            showMessage('Erro ao comunicar com a API.', 'error');
        }
    }

    function getCreateData() {
        return {
            nome: serviceNameInput.value.trim(),
            descricao: serviceDescriptionInput.value.trim(),
            preco: servicePriceInput.value,
            duracao: serviceDurationInput.value
        };
    }

    function validateServiceData(data) {
        if (!data.nome) {
            showMessage('Informe o nome do serviço.', 'error');
            return false;
        }

        if (data.preco === '' || Number(data.preco) < 0) {
            showMessage('Informe um preço válido.', 'error');
            return false;
        }

        if (!data.duracao || Number(data.duracao) <= 0) {
            showMessage('Informe a duração em minutos.', 'error');
            return false;
        }

        return true;
    }

    async function saveNewService(event) {
        event.preventDefault();

        const data = getCreateData();

        if (!validateServiceData(data)) {
            return;
        }

        try {
            const response = await fetch(`${API_BASE}?action=api_service_store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await readJsonResponse(response);

            if (!result.success) {
                showMessage(result.message || 'Não foi possível cadastrar o serviço.', 'error');
                return;
            }

            showMessage(result.message || 'Serviço cadastrado com sucesso.');
            clearCreateForm();
            await loadServices();

        } catch (error) {
            showMessage('Erro ao cadastrar serviço.', 'error');
        }
    }

    async function saveEditService(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const id = form.dataset.id;

        const data = {
            nome: form.elements.nome.value.trim(),
            preco: form.elements.preco.value,
            duracao: form.elements.duracao.value,
            descricao: form.elements.descricao.value.trim()
        };

        if (!id) {
            showMessage('ID do serviço não encontrado.', 'error');
            return;
        }

        if (!validateServiceData(data)) {
            return;
        }

        try {
            const response = await fetch(`${API_BASE}?action=api_service_update&id=${encodeURIComponent(id)}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await readJsonResponse(response);

            if (!result.success) {
                showMessage(result.message || 'Não foi possível atualizar o serviço.', 'error');
                return;
            }

            showMessage(result.message || 'Serviço atualizado com sucesso.');
            await loadServices();

        } catch (error) {
            showMessage('Erro ao atualizar serviço.', 'error');
        }
    }

    async function deleteService(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const id = form.dataset.id;
        const name = form.dataset.name || 'este serviço';

        if (!confirm(`Deseja realmente excluir ${name}?`)) {
            return;
        }

        try {
            const response = await fetch(`${API_BASE}?action=api_service_delete&id=${encodeURIComponent(id)}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await readJsonResponse(response);

            if (!result.success) {
                showMessage(result.message || 'Não foi possível excluir o serviço.', 'error');
                return;
            }

            showMessage(result.message || 'Serviço excluído com sucesso.');
            await loadServices();

        } catch (error) {
            showMessage('Erro ao excluir serviço.', 'error');
        }
    }

    function clearCreateForm() {
        serviceNameInput.value = '';
        serviceDescriptionInput.value = '';
        servicePriceInput.value = '';
        serviceDurationInput.value = '';
    }

    serviceForm.addEventListener('submit', saveNewService);

    clearButton.addEventListener('click', function () {
        clearCreateForm();
        hideMessage();
    });

    reloadButton.addEventListener('click', function () {
        loadServices();
    });

    searchInput.addEventListener('input', filterServices);

    servicesGrid.addEventListener('submit', function (event) {
        if (event.target.classList.contains('edit-form')) {
            saveEditService(event);
        }

        if (event.target.classList.contains('delete-form')) {
            deleteService(event);
        }
    });

    loadServices();
</script>

</body>
</html>