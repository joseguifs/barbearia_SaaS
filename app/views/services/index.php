<?php
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
    <title>Gerenciar Serviços - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { min-height: 100vh; background: #0f0f0f; color: #fff; font-family: Arial, Helvetica, sans-serif; }

        .topbar {
            position: fixed; top: 0; left: 0; width: 100%; height: 82px; padding: 0 42px;
            display: flex; align-items: center; justify-content: space-between; z-index: 1000;
            background: rgba(10, 10, 10, .52); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            box-shadow: 0 12px 40px rgba(0, 0, 0, .35), inset 0 1px 0 rgba(255, 255, 255, .03);
        }
        .topbar::before {
            content: ""; position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(197, 157, 95, .08), transparent 20%, transparent 80%, rgba(197, 157, 95, .05));
            pointer-events: none;
        }
        .logo { position: relative; display: flex; align-items: center; gap: 12px; font-size: 1.5rem; font-weight: 800; letter-spacing: 2px; color: #fff; z-index: 2; }
        .logo::before { content: ""; width: 10px; height: 10px; border-radius: 50%; background: #c59d5f; box-shadow: 0 0 14px rgba(197, 157, 95, .8); }
        .topbar-right { position: relative; display: flex; align-items: center; gap: 18px; z-index: 2; }
        .nav { display: flex; align-items: center; gap: 10px; }
        .nav a { position: relative; text-decoration: none; color: rgba(255, 255, 255, .72); padding: 11px 18px; border-radius: 14px; font-size: .92rem; font-weight: 600; transition: .25s ease; border: 1px solid transparent; }
        .nav a:hover, .nav a.active { color: #fff; background: rgba(255, 255, 255, .06); border-color: rgba(255, 255, 255, .06); transform: translateY(-1px); }

        .hero { position: relative; min-height: 520px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 120px 20px 155px; }
        .hero::before {
            content: ""; position: absolute; inset: 0;
            background: linear-gradient(rgba(0, 0, 0, .48), rgba(0, 0, 0, .82)), url('assets/images/backgroundLogin.jpeg') center center/cover no-repeat;
            transform: scale(1.06); filter: blur(3px) brightness(.55);
        }
        .hero-overlay { position: absolute; inset: 0; background: radial-gradient(circle at top right, rgba(197, 157, 95, .14), transparent 35%); }
        .hero-content { position: relative; z-index: 2; text-align: center; max-width: 900px; padding: 30px 20px; }
        .hero-content h1 { font-size: 3.6rem; line-height: 1.1; color: #fff; margin: 14px 0 20px; font-weight: 800; }
        .hero-content p { max-width: 820px; margin: 0 auto; color: rgba(255, 255, 255, .82); font-size: 1.05rem; line-height: 1.8; }
        .section-tag { color: #c59d5f; font-size: .8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }

        .admin-page { max-width: 1240px; margin: 0 auto; padding: 46px 22px 80px; }
        .manager-layout { position: relative; z-index: 10; margin-top: -60px; display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }
        .form-panel, .list-panel, .service-card {
            background: rgba(18, 18, 18, .62); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, .22), inset 0 1px 0 rgba(255, 255, 255, .03);
            border-radius: 26px;
        }
        .form-panel, .list-panel { padding: 28px; }
        .panel-title { font-size: 1.55rem; color: #fff; margin: 8px 0 24px; position: relative; }
        .panel-title::after { content: ""; display: block; width: 70px; height: 4px; margin-top: 12px; border-radius: 999px; background: linear-gradient(90deg, #c59d5f, transparent); }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 9px; color: rgba(255, 255, 255, .76); font-size: .9rem; font-weight: 700; }
        .form-group input, .form-group textarea, .toolbar input {
            width: 100%; padding: 0 15px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, .09);
            outline: none; background: rgba(255, 255, 255, .05); color: #fff; font-size: .95rem; transition: .25s ease;
        }
        .form-group input { height: 52px; }
        .form-group textarea { min-height: 110px; padding-top: 14px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus, .toolbar input:focus { border-color: rgba(197, 157, 95, .45); background: rgba(255, 255, 255, .07); box-shadow: 0 0 0 3px rgba(197, 157, 95, .09); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .input-hint { display: block; margin-top: 8px; color: rgba(255, 255, 255, .50); font-size: .78rem; line-height: 1.5; }

        .form-actions, .toolbar, .card-actions, .modal-actions { display: flex; gap: 12px; }
        .form-actions { margin-top: 24px; }
        .toolbar { margin-bottom: 24px; }
        .btn-primary, .btn-secondary, .btn-danger, .btn-small, .btn-danger-soft { border: none; border-radius: 14px; font-weight: 800; cursor: pointer; transition: .25s ease; }
        .btn-primary { flex: 1; min-height: 52px; background: linear-gradient(135deg, #c59d5f, #8b5e34); color: #fff; }
        .btn-secondary { min-height: 52px; padding: 0 18px; background: rgba(255, 255, 255, .06); color: #fff; border: 1px solid rgba(255, 255, 255, .08); }
        .btn-primary:hover, .btn-secondary:hover, .btn-small:hover, .btn-danger:hover, .btn-danger-soft:hover { transform: translateY(-2px); }
        .toolbar input { flex: 1; height: 50px; }
        .toolbar button { height: 50px; padding: 0 18px; }

        .services-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .service-card { padding: 22px; position: relative; overflow: hidden; transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease; }
        .service-card:hover { transform: translateY(-6px); border-color: rgba(197, 157, 95, .20); box-shadow: 0 24px 60px rgba(0, 0, 0, .28), 0 0 0 1px rgba(197, 157, 95, .05); }
        .service-avatar { width: 58px; height: 58px; border-radius: 18px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #c59d5f, #8b5e34); color: #fff; font-size: 1rem; font-weight: 900; margin-bottom: 16px; }
        .service-card h3 { font-size: 1.22rem; margin-bottom: 8px; }
        .service-price { color: rgba(231, 196, 141, .82); font-size: .9rem; font-weight: 800; margin-bottom: 10px; }
        .service-description { color: rgba(255, 255, 255, .68); font-size: .92rem; line-height: 1.6; margin-bottom: 16px; }
        .service-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
        .service-tag { padding: 7px 10px; border-radius: 999px; background: rgba(197, 157, 95, .12); border: 1px solid rgba(197, 157, 95, .20); color: #e7c48d; font-size: .76rem; font-weight: 700; }
        .btn-small { min-height: 40px; padding: 0 14px; background: rgba(255, 255, 255, .06); color: #fff; border: 1px solid rgba(255, 255, 255, .08); }
        .btn-danger { min-height: 40px; padding: 0 14px; background: rgba(180, 62, 62, .16); color: #ffb7b7; border: 1px solid rgba(255, 90, 90, .18); }
        .btn-danger-soft { min-height: 52px; padding: 0 18px; background: linear-gradient(135deg, rgba(165, 58, 58, .95), rgba(115, 32, 32, .95)); color: #fff; }
        .empty-state { padding: 42px 22px; text-align: center; border-radius: 22px; background: rgba(255, 255, 255, .04); border: 1px dashed rgba(255, 255, 255, .14); color: rgba(255, 255, 255, .68); line-height: 1.6; grid-column: 1 / -1; }
        .loading-state { color: rgba(255, 255, 255, .68); padding: 24px 0; }

        .modal-backdrop { position: fixed; inset: 0; background: rgba(0, 0, 0, .68); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 22px; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity .25s ease; }
        .modal-backdrop.show { opacity: 1; pointer-events: auto; }
        .modal-box { width: 100%; max-width: 520px; background: rgba(18, 18, 18, .80); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border: 1px solid rgba(255, 255, 255, .08); border-radius: 26px; padding: 28px; box-shadow: 0 25px 70px rgba(0, 0, 0, .48), inset 0 1px 0 rgba(255, 255, 255, .04); color: #fff; transform: translateY(14px) scale(.97); transition: transform .25s ease; }
        .modal-backdrop.show .modal-box { transform: translateY(0) scale(1); }
        .modal-text { color: rgba(255, 255, 255, .75); line-height: 1.7; margin-top: -6px; margin-bottom: 8px; }
        .modal-warning { margin-top: 14px; padding: 14px 16px; border-radius: 16px; background: rgba(255, 90, 90, .08); border: 1px solid rgba(255, 90, 90, .14); color: #ffcdcd; font-size: .9rem; line-height: 1.6; }
        .modal-actions { margin-top: 24px; }
        .modal-actions .btn-secondary, .modal-actions .btn-primary, .modal-actions .btn-danger-soft { flex: 1; }
        .toast-message { position: fixed; right: 20px; bottom: 20px; min-width: 260px; max-width: 380px; padding: 16px 18px; border-radius: 16px; background: rgba(18, 18, 18, .72); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border: 1px solid rgba(255, 255, 255, .08); color: #fff; box-shadow: 0 20px 40px rgba(0, 0, 0, .35); z-index: 10000; opacity: 0; transform: translateY(12px); transition: .25s ease; }
        .toast-message.show { opacity: 1; transform: translateY(0); }
        .footer { padding: 70px 20px 50px; border-top: 1px solid rgba(255, 255, 255, .06); background: linear-gradient(to bottom, rgba(15, 15, 15, .92), rgba(10, 10, 10, 1)); }
        .footer-container { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 28px; flex-wrap: wrap; }
        .footer-logo { font-size: 1.3rem; font-weight: 800; letter-spacing: 2px; color: #fff; }
        .footer-copy { color: rgba(255, 255, 255, .62); }
        .reveal-item { opacity: 0; transform: translateY(26px); transition: opacity .7s ease, transform .7s ease; }
        .reveal-item.is-visible { opacity: 1; transform: translateY(0); }

        @media(max-width: 980px) {
            .hero { min-height: 500px; padding: 115px 20px 130px; }
            .manager-layout { grid-template-columns: 1fr; margin-top: -40px; }
            .services-grid { grid-template-columns: 1fr; }
            .hero-content h1 { font-size: 2.6rem; }
        }
        @media(max-width: 600px) {
            .topbar { height: 70px; padding: 0 14px; }
            .logo { font-size: 1.05rem; letter-spacing: 1.5px; }
            .nav { display: none; }
            .hero { min-height: 470px; padding: 95px 16px 115px; }
            .hero-content { padding: 20px 10px; }
            .hero-content h1 { font-size: 2.15rem; }
            .hero-content p { font-size: .96rem; line-height: 1.7; }
            .manager-layout { margin-top: -28px; }
            .form-row { grid-template-columns: 1fr; }
            .form-actions, .toolbar, .card-actions, .modal-actions { flex-direction: column; }
            .btn-primary, .btn-secondary, .btn-small, .btn-danger, .btn-danger-soft { width: 100%; }
            .toast-message { left: 16px; right: 16px; max-width: none; min-width: 0; }
        }
    </style>
</head>
<body>
    <header class="topbar reveal-item">
        <div class="logo">BARBERTIME</div>
        <div class="topbar-right">
            <nav class="nav">
                <a href="index.php?action=admin_index">Painel</a>
                <a href="index.php?action=scheduling_list">Agendamentos</a>
                <a href="index.php?action=barbers">Barbeiros</a>
                <a href="index.php?action=services" class="active">Serviços</a>
            </nav>
        </div>
    </header>

    <section class="hero reveal-item">
        <div class="hero-overlay"></div>
        <div class="hero-content reveal-item">
            <span class="section-tag">Administração</span>
            <h1>Gerenciar serviços</h1>
            <p>
                Cadastre, edite, remova e visualize os serviços disponíveis na barbearia.
                Cada card apresenta nome, descrição, preço e duração aproximada do atendimento.
            </p>
        </div>
    </section>

    <main class="admin-page">
        <section class="manager-layout" id="gerenciar">
            <aside class="form-panel reveal-item">
                <span class="section-tag">Cadastro</span>
                <h2 class="panel-title">Novo serviço</h2>

                <form id="service-form">
                    <div class="form-group">
                        <label for="service-name">Nome do serviço</label>
                        <input type="text" id="service-name" placeholder="Ex: Corte executivo" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="service-description">Descrição</label>
                        <textarea id="service-description" placeholder="Ex: Corte clássico com acabamento profissional"></textarea>
                        <small class="input-hint">Essa descrição será exibida no card do serviço.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="service-price">Preço</label>
                            <input type="number" id="service-price" placeholder="Ex: 45.00" step="0.01" min="0">
                        </div>

                        <div class="form-group">
                            <label for="service-duration">Duração</label>
                            <input type="number" id="service-duration" placeholder="Minutos" min="1">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary" id="submit-button">Cadastrar</button>
                        <button type="button" class="btn-secondary" id="clear-button">Limpar</button>
                    </div>
                </form>
            </aside>

            <section class="list-panel reveal-item" id="servicos">
                <span class="section-tag">Catálogo</span>
                <h2 class="panel-title">Serviços cadastrados</h2>

                <div class="toolbar">
                    <input type="text" id="search-input" placeholder="Buscar serviço pelo nome ou descrição...">
                    <button type="button" class="btn-secondary" id="reload-button">Atualizar</button>
                </div>

                <div id="loading-state" class="loading-state">Carregando serviços...</div>
                <div id="services-grid" class="services-grid"></div>
            </section>
        </section>
    </main>

    <footer class="footer reveal-item">
        <div class="footer-container">
            <div class="footer-logo">BARBERTIME</div>
            <p class="footer-copy">© BarberTime cortes & barba. Painel administrativo.</p>
        </div>
    </footer>

    <div id="edit-modal-backdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true">
            <span class="section-tag">Editar serviço</span>
            <h2 class="panel-title">Alterar informações</h2>

            <form id="edit-service-form">
                <input type="hidden" id="edit-service-id">

                <div class="form-group">
                    <label for="edit-service-name">Nome do serviço</label>
                    <input type="text" id="edit-service-name" placeholder="Ex: Corte executivo" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="edit-service-description">Descrição</label>
                    <textarea id="edit-service-description" placeholder="Ex: Corte clássico com acabamento profissional"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-service-price">Preço</label>
                        <input type="number" id="edit-service-price" placeholder="Ex: 45.00" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="edit-service-duration">Duração</label>
                        <input type="number" id="edit-service-duration" placeholder="Minutos" min="1">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancel-edit-button">Cancelar</button>
                    <button type="submit" class="btn-primary" id="save-edit-button">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal-backdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true">
            <span class="section-tag">Excluir serviço</span>
            <h2 class="panel-title">Confirmar exclusão</h2>

            <p class="modal-text">
                Deseja realmente excluir o serviço <strong id="delete-service-name">-</strong>?
            </p>

            <div class="modal-warning">
                Se este serviço já estiver vinculado a um agendamento, o banco pode bloquear a exclusão.
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancel-delete-button">Cancelar</button>
                <button type="button" class="btn-danger-soft" id="confirm-delete-button">Excluir serviço</button>
            </div>
        </div>
    </div>

    <div id="toast-message" class="toast-message"></div>

    <script>
        const API_BASE = 'index.php';

        const serviceForm = document.getElementById('service-form');
        const serviceNameInput = document.getElementById('service-name');
        const serviceDescriptionInput = document.getElementById('service-description');
        const servicePriceInput = document.getElementById('service-price');
        const serviceDurationInput = document.getElementById('service-duration');

        const submitButton = document.getElementById('submit-button');
        const clearButton = document.getElementById('clear-button');
        const reloadButton = document.getElementById('reload-button');
        const searchInput = document.getElementById('search-input');
        const servicesGrid = document.getElementById('services-grid');
        const loadingState = document.getElementById('loading-state');
        const toastMessage = document.getElementById('toast-message');

        const editModalBackdrop = document.getElementById('edit-modal-backdrop');
        const editServiceForm = document.getElementById('edit-service-form');
        const editServiceIdInput = document.getElementById('edit-service-id');
        const editServiceNameInput = document.getElementById('edit-service-name');
        const editServiceDescriptionInput = document.getElementById('edit-service-description');
        const editServicePriceInput = document.getElementById('edit-service-price');
        const editServiceDurationInput = document.getElementById('edit-service-duration');
        const cancelEditButton = document.getElementById('cancel-edit-button');
        const saveEditButton = document.getElementById('save-edit-button');

        const deleteModalBackdrop = document.getElementById('delete-modal-backdrop');
        const deleteServiceName = document.getElementById('delete-service-name');
        const cancelDeleteButton = document.getElementById('cancel-delete-button');
        const confirmDeleteButton = document.getElementById('confirm-delete-button');

        let services = [];
        let serviceIdToDelete = null;

        function showToast(message) {
            toastMessage.textContent = message;
            toastMessage.classList.add('show');
            setTimeout(() => toastMessage.classList.remove('show'), 2800);
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
                    <div class="empty-state">
                        Nenhum serviço encontrado.<br>
                        Cadastre um novo serviço pelo formulário ao lado.
                    </div>
                `;
                return;
            }

            list.forEach((service) => {
                const card = document.createElement('article');
                card.className = 'service-card reveal-item is-visible';

                card.innerHTML = `
                    <div class="service-avatar">R$</div>
                    <h3>${escapeHtml(service.nome)}</h3>
                    <p class="service-price">${formatMoney(service.preco)}</p>
                    <p class="service-description">
                        ${service.descricao ? escapeHtml(service.descricao) : 'Serviço sem descrição cadastrada.'}
                    </p>
                    <div class="service-tags">
                        <span class="service-tag">${Number(service.duracao || 0)} min</span>
                        <span class="service-tag">ID ${escapeHtml(service.id_servico)}</span>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-small" data-action="edit" data-id="${service.id_servico}">Editar</button>
                        <button type="button" class="btn-danger" data-action="delete" data-id="${service.id_servico}">Excluir</button>
                    </div>
                `;

                servicesGrid.appendChild(card);
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

        async function loadServices() {
            loadingState.style.display = 'block';
            servicesGrid.innerHTML = '';

            try {
                const response = await fetch(`${API_BASE}?action=api_service_index`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Erro ao carregar serviços.');
                    loadingState.style.display = 'none';
                    return;
                }

                services = result.data || [];
                filterServices();
            } catch (error) {
                console.error(error);
                loadingState.style.display = 'none';
                showToast('Erro ao comunicar com a API.');
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

        function getEditData() {
            return {
                nome: editServiceNameInput.value.trim(),
                descricao: editServiceDescriptionInput.value.trim(),
                preco: editServicePriceInput.value,
                duracao: editServiceDurationInput.value
            };
        }

        function validateServiceData(data, focusElement) {
            if (!data.nome) {
                showToast('Informe o nome do serviço.');
                focusElement.focus();
                return false;
            }

            if (!data.preco || Number(data.preco) < 0) {
                showToast('Informe um preço válido.');
                return false;
            }

            if (!data.duracao || Number(data.duracao) <= 0) {
                showToast('Informe a duração em minutos.');
                return false;
            }

            return true;
        }

        async function saveNewService(event) {
            event.preventDefault();
            const data = getCreateData();

            if (!validateServiceData(data, serviceNameInput)) return;

            submitButton.disabled = true;
            submitButton.textContent = 'Cadastrando...';

            try {
                const response = await fetch(`${API_BASE}?action=api_service_store`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível cadastrar.');
                    return;
                }

                showToast(result.message || 'Serviço cadastrado com sucesso.');
                clearCreateForm();
                await loadServices();
            } catch (error) {
                console.error(error);
                showToast('Erro ao cadastrar serviço.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Cadastrar';
            }
        }

        function openEditModal(id) {
            const service = services.find(item => Number(item.id_servico) === Number(id));

            if (!service) {
                showToast('Serviço não encontrado na lista.');
                return;
            }

            editServiceIdInput.value = service.id_servico;
            editServiceNameInput.value = service.nome || '';
            editServiceDescriptionInput.value = service.descricao || '';
            editServicePriceInput.value = service.preco || '';
            editServiceDurationInput.value = service.duracao || '';

            editModalBackdrop.classList.add('show');
            editModalBackdrop.setAttribute('aria-hidden', 'false');
            setTimeout(() => editServiceNameInput.focus(), 150);
        }

        function closeEditModal() {
            editModalBackdrop.classList.remove('show');
            editModalBackdrop.setAttribute('aria-hidden', 'true');
            editServiceIdInput.value = '';
            editServiceNameInput.value = '';
            editServiceDescriptionInput.value = '';
            editServicePriceInput.value = '';
            editServiceDurationInput.value = '';
        }

        async function saveEditService(event) {
            event.preventDefault();

            const id = editServiceIdInput.value;
            const data = getEditData();

            if (!id) {
                showToast('ID do serviço não encontrado.');
                return;
            }

            if (!validateServiceData(data, editServiceNameInput)) return;

            saveEditButton.disabled = true;
            saveEditButton.textContent = 'Salvando...';

            try {
                const response = await fetch(`${API_BASE}?action=api_service_update&id=${encodeURIComponent(id)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível atualizar.');
                    return;
                }

                showToast(result.message || 'Serviço atualizado com sucesso.');
                closeEditModal();
                await loadServices();
            } catch (error) {
                console.error(error);
                showToast('Erro ao atualizar serviço.');
            } finally {
                saveEditButton.disabled = false;
                saveEditButton.textContent = 'Salvar alterações';
            }
        }

        function openDeleteModal(id) {
            const service = services.find(item => Number(item.id_servico) === Number(id));

            if (!service) {
                showToast('Serviço não encontrado na lista.');
                return;
            }

            serviceIdToDelete = id;
            deleteServiceName.textContent = service.nome || 'este serviço';
            deleteModalBackdrop.classList.add('show');
            deleteModalBackdrop.setAttribute('aria-hidden', 'false');
        }

        function closeDeleteModal() {
            serviceIdToDelete = null;
            deleteServiceName.textContent = '-';
            deleteModalBackdrop.classList.remove('show');
            deleteModalBackdrop.setAttribute('aria-hidden', 'true');
        }

        async function confirmDeleteService() {
            if (!serviceIdToDelete) {
                showToast('ID do serviço não encontrado.');
                return;
            }

            confirmDeleteButton.disabled = true;
            confirmDeleteButton.textContent = 'Excluindo...';

            try {
                const response = await fetch(`${API_BASE}?action=api_service_delete&id=${encodeURIComponent(serviceIdToDelete)}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível excluir.');
                    return;
                }

                showToast(result.message || 'Serviço excluído com sucesso.');
                closeDeleteModal();
                await loadServices();
            } catch (error) {
                console.error(error);
                showToast('Erro ao excluir serviço.');
            } finally {
                confirmDeleteButton.disabled = false;
                confirmDeleteButton.textContent = 'Excluir serviço';
            }
        }

        function clearCreateForm() {
            serviceNameInput.value = '';
            serviceDescriptionInput.value = '';
            servicePriceInput.value = '';
            serviceDurationInput.value = '';
        }

        function activateReveals() {
            const items = document.querySelectorAll('.reveal-item');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            items.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.04}s`;
                observer.observe(item);
            });
        }

        serviceForm.addEventListener('submit', saveNewService);
        editServiceForm.addEventListener('submit', saveEditService);
        clearButton.addEventListener('click', clearCreateForm);
        reloadButton.addEventListener('click', () => { loadServices(); showToast('Lista atualizada.'); });
        searchInput.addEventListener('input', filterServices);
        cancelEditButton.addEventListener('click', closeEditModal);
        cancelDeleteButton.addEventListener('click', closeDeleteModal);
        confirmDeleteButton.addEventListener('click', confirmDeleteService);

        editModalBackdrop.addEventListener('click', (event) => {
            if (event.target === editModalBackdrop) closeEditModal();
        });

        deleteModalBackdrop.addEventListener('click', (event) => {
            if (event.target === deleteModalBackdrop) closeDeleteModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (editModalBackdrop.classList.contains('show')) closeEditModal();
                if (deleteModalBackdrop.classList.contains('show')) closeDeleteModal();
            }
        });

        servicesGrid.addEventListener('click', (event) => {
            const button = event.target.closest('button');
            if (!button) return;

            const action = button.dataset.action;
            const id = button.dataset.id;

            if (action === 'edit') openEditModal(id);
            if (action === 'delete') openDeleteModal(id);
        });

        activateReveals();
        loadServices();
    </script>
</body>
</html>
