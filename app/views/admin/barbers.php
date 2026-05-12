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
    <title>Gerenciar Barbeiros - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background: #0f0f0f;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 82px;
            padding: 0 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            background: rgba(10, 10, 10, .52);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            box-shadow:
                0 12px 40px rgba(0, 0, 0, .35),
                inset 0 1px 0 rgba(255, 255, 255, .03);
        }

        .topbar::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg,
                    rgba(197, 157, 95, .08),
                    transparent 20%,
                    transparent 80%,
                    rgba(197, 157, 95, .05)
                );
            pointer-events: none;
        }

        .logo {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #fff;
            z-index: 2;
        }

        .logo::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #c59d5f;
            box-shadow: 0 0 14px rgba(197, 157, 95, .8);
        }

        .topbar-right {
            position: relative;
            display: flex;
            align-items: center;
            gap: 18px;
            z-index: 2;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav a {
            position: relative;
            text-decoration: none;
            color: rgba(255, 255, 255, .72);
            padding: 11px 18px;
            border-radius: 14px;
            font-size: .92rem;
            font-weight: 600;
            transition: .25s ease;
            border: 1px solid transparent;
        }

        .nav a:hover {
            color: #fff;
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .06);
            transform: translateY(-1px);
        }

        .hero {
            position: relative;
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 120px 20px 155px;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(
                    rgba(0, 0, 0, .48),
                    rgba(0, 0, 0, .82)
                ),
                url('/BARBEARIA_SAAS/public/assets/images/backgroundLogin.jpeg')
                center center/cover no-repeat;
            transform: scale(1.06);
            filter: blur(3px) brightness(.55);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(197, 157, 95, .14),
                    transparent 35%
                );
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            padding: 30px 20px;
        }

        .hero-content h1 {
            font-size: 3.6rem;
            line-height: 1.1;
            color: #fff;
            margin: 14px 0 20px;
            font-weight: 800;
        }

        .hero-content p {
            max-width: 820px;
            margin: 0 auto;
            color: rgba(255, 255, 255, .82);
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .section-tag {
            color: #c59d5f;
            font-size: .8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-page {
            max-width: 1240px;
            margin: 0 auto;
            padding: 46px 22px 80px;
        }

        .manager-layout {
            position: relative;
            z-index: 10;
            margin-top: -60px;
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
            align-items: start;
        }

        .form-panel,
        .list-panel,
        .barber-card {
            background: rgba(18, 18, 18, .62);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow:
                0 20px 50px rgba(0, 0, 0, .22),
                inset 0 1px 0 rgba(255, 255, 255, .03);
            border-radius: 26px;
        }

        .form-panel,
        .list-panel {
            padding: 28px;
        }

        .panel-title {
            font-size: 1.55rem;
            color: #fff;
            margin: 8px 0 24px;
            position: relative;
        }

        .panel-title::after {
            content: "";
            display: block;
            width: 70px;
            height: 4px;
            margin-top: 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #c59d5f, transparent);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            color: rgba(255, 255, 255, .76);
            font-size: .9rem;
            font-weight: 700;
        }

        .form-group input {
            width: 100%;
            height: 52px;
            padding: 0 15px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .09);
            outline: none;
            background: rgba(255, 255, 255, .05);
            color: #fff;
            font-size: .95rem;
            transition: .25s ease;
        }

        .form-group input:focus {
            border-color: rgba(197, 157, 95, .45);
            background: rgba(255, 255, 255, .07);
            box-shadow: 0 0 0 3px rgba(197, 157, 95, .09);
        }

        .input-hint {
            display: block;
            margin-top: 8px;
            color: rgba(255, 255, 255, .50);
            font-size: .78rem;
            line-height: 1.5;
        }

        .form-actions,
        .toolbar,
        .card-actions,
        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .form-actions {
            margin-top: 24px;
        }

        .toolbar {
            margin-bottom: 24px;
        }

        .btn-primary,
        .btn-secondary,
        .btn-danger,
        .btn-small,
        .btn-danger-soft {
            border: none;
            border-radius: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: .25s ease;
        }

        .btn-primary {
            flex: 1;
            min-height: 52px;
            background: linear-gradient(135deg, #c59d5f, #8b5e34);
            color: #fff;
        }

        .btn-secondary {
            min-height: 52px;
            padding: 0 18px;
            background: rgba(255, 255, 255, .06);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .btn-primary:hover,
        .btn-secondary:hover,
        .btn-small:hover,
        .btn-danger:hover,
        .btn-danger-soft:hover {
            transform: translateY(-2px);
        }

        .toolbar input {
            flex: 1;
            height: 50px;
            padding: 0 15px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .09);
            outline: none;
            background: rgba(255, 255, 255, .05);
            color: #fff;
        }

        .toolbar button {
            height: 50px;
            padding: 0 18px;
        }

        .barbers-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .barber-card {
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease;
        }

        .barber-card:hover {
            transform: translateY(-6px);
            border-color: rgba(197, 157, 95, .20);
            box-shadow:
                0 24px 60px rgba(0, 0, 0, .28),
                0 0 0 1px rgba(197, 157, 95, .05);
        }

        .barber-avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #c59d5f, #8b5e34);
            color: #fff;
            font-size: 1.4rem;
            font-weight: 900;
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
        }

        .barber-card h3 {
            font-size: 1.22rem;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }

        .barber-email {
            color: rgba(231, 196, 141, .82);
            font-size: .84rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
            word-break: break-word;
        }

        .barber-description {
            color: rgba(255, 255, 255, .68);
            font-size: .92rem;
            line-height: 1.6;
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
        }

        .service-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
            position: relative;
            z-index: 2;
        }

        .service-tag {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(197, 157, 95, .12);
            border: 1px solid rgba(197, 157, 95, .20);
            color: #e7c48d;
            font-size: .76rem;
            font-weight: 700;
        }

        .btn-small {
            min-height: 40px;
            padding: 0 14px;
            background: rgba(255, 255, 255, .06);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .btn-danger {
            min-height: 40px;
            padding: 0 14px;
            background: rgba(180, 62, 62, .16);
            color: #ffb7b7;
            border: 1px solid rgba(255, 90, 90, .18);
        }

        .btn-danger-soft {
            min-height: 52px;
            padding: 0 18px;
            background: linear-gradient(135deg, rgba(165, 58, 58, .95), rgba(115, 32, 32, .95));
            color: #fff;
        }

        .empty-state {
            padding: 42px 22px;
            text-align: center;
            border-radius: 22px;
            background: rgba(255, 255, 255, .04);
            border: 1px dashed rgba(255, 255, 255, .14);
            color: rgba(255, 255, 255, .68);
            line-height: 1.6;
        }

        .loading-state {
            color: rgba(255, 255, 255, .68);
            padding: 24px 0;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .68);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s ease;
        }

        .modal-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            width: 100%;
            max-width: 460px;
            background: rgba(18, 18, 18, .80);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 26px;
            padding: 28px;
            box-shadow:
                0 25px 70px rgba(0, 0, 0, .48),
                inset 0 1px 0 rgba(255, 255, 255, .04);
            color: #fff;
            transform: translateY(14px) scale(.97);
            transition: transform .25s ease;
        }

        .modal-backdrop.show .modal-box {
            transform: translateY(0) scale(1);
        }

        .modal-text {
            color: rgba(255, 255, 255, .75);
            line-height: 1.7;
            margin-top: -6px;
            margin-bottom: 8px;
        }

        .modal-warning {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 90, 90, .08);
            border: 1px solid rgba(255, 90, 90, .14);
            color: #ffcdcd;
            font-size: .9rem;
            line-height: 1.6;
        }

        .modal-actions {
            margin-top: 24px;
        }

        .modal-actions .btn-secondary,
        .modal-actions .btn-primary,
        .modal-actions .btn-danger-soft {
            flex: 1;
        }

        .toast-message {
            position: fixed;
            right: 20px;
            bottom: 20px;
            min-width: 260px;
            max-width: 380px;
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(18, 18, 18, .72);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .08);
            color: #fff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
            z-index: 10000;
            opacity: 0;
            transform: translateY(12px);
            transition: .25s ease;
        }

        .toast-message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .footer {
            padding: 70px 20px 50px;
            border-top: 1px solid rgba(255, 255, 255, .06);
            background: linear-gradient(to bottom, rgba(15, 15, 15, .92), rgba(10, 10, 10, 1));
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            flex-wrap: wrap;
        }

        .footer-logo {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #fff;
        }

        .footer-copy {
            color: rgba(255, 255, 255, .62);
        }

        .reveal-item {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media(max-width: 980px) {
            .hero {
                min-height: 500px;
                padding: 115px 20px 130px;
            }

            .manager-layout {
                grid-template-columns: 1fr;
                margin-top: -40px;
            }

            .barbers-grid {
                grid-template-columns: 1fr;
            }

            .hero-content h1 {
                font-size: 2.6rem;
            }
        }

        @media(max-width: 600px) {
            .topbar {
                height: 70px;
                padding: 0 14px;
            }

            .logo {
                font-size: 1.05rem;
                letter-spacing: 1.5px;
            }

            .nav {
                display: none;
            }

            .hero {
                min-height: 470px;
                padding: 95px 16px 115px;
            }

            .hero-content {
                padding: 20px 10px;
            }

            .hero-content h1 {
                font-size: 2.15rem;
            }

            .hero-content p {
                font-size: .96rem;
                line-height: 1.7;
            }

            .manager-layout {
                margin-top: -28px;
            }

            .form-actions,
            .toolbar,
            .card-actions,
            .modal-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary,
            .btn-small,
            .btn-danger,
            .btn-danger-soft {
                width: 100%;
            }

            .toast-message {
                left: 16px;
                right: 16px;
                max-width: none;
                min-width: 0;
            }
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
                <a href="#barbeiros">Barbeiros</a>
            </nav>
        </div>
    </header>

    <section class="hero hero-admin reveal-item">
        <div class="hero-overlay"></div>

        <div class="hero-content reveal-item">
            <span class="section-tag">Administração</span>

            <h1>Gerenciar barbeiros</h1>

            <p>
                Cadastre, edite, remova e visualize os barbeiros disponíveis no sistema.
                Os cards abaixo apresentam cada profissional com uma breve descrição,
                e-mail de acesso futuro e seus serviços relacionados.
            </p>
        </div>
    </section>

    <main class="admin-page">
        <section class="manager-layout" id="gerenciar">
            <aside class="form-panel reveal-item">
                <span class="section-tag">Cadastro</span>
                <h2 class="panel-title">Novo barbeiro</h2>

                <form id="barber-form">
                    <div class="form-group">
                        <label for="barber-name">Nome do barbeiro</label>
                        <input type="text" id="barber-name" placeholder="Ex: João Silva" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="barber-email">E-mail do barbeiro</label>
                        <input type="email" id="barber-email" placeholder="Ex: barbeiro@email.com" autocomplete="off">
                        <small class="input-hint">
                            Esse e-mail será usado futuramente para o login do barbeiro.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="barber-services">Serviços relacionados</label>
                        <input type="text" id="barber-services" placeholder="Ex: 1,2,3" autocomplete="off">
                        <small class="input-hint">
                            Informe os IDs dos serviços separados por vírgula. Exemplo: 1,2,3.
                        </small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary" id="submit-button">
                            Cadastrar
                        </button>

                        <button type="button" class="btn-secondary" id="clear-button">
                            Limpar
                        </button>
                    </div>
                </form>
            </aside>

            <section class="list-panel reveal-item" id="barbeiros">
                <span class="section-tag">Equipe</span>
                <h2 class="panel-title">Barbeiros cadastrados</h2>

                <div class="toolbar">
                    <input type="text" id="search-input" placeholder="Buscar barbeiro pelo nome ou e-mail...">

                    <button type="button" class="btn-secondary" id="reload-button">
                        Atualizar
                    </button>
                </div>

                <div id="loading-state" class="loading-state">
                    Carregando barbeiros...
                </div>

                <div id="barbers-grid" class="barbers-grid"></div>
            </section>
        </section>
    </main>

    <footer class="footer reveal-item">
        <div class="footer-container">
            <div class="footer-logo">BARBERTIME</div>

            <p class="footer-copy">
                © BarberTime cortes & barba. Painel administrativo.
            </p>
        </div>
    </footer>

    <div id="edit-modal-backdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true">
            <span class="section-tag">Editar barbeiro</span>
            <h2 class="panel-title">Alterar informações</h2>

            <form id="edit-barber-form">
                <input type="hidden" id="edit-barber-id">

                <div class="form-group">
                    <label for="edit-barber-name">Nome do barbeiro</label>
                    <input type="text" id="edit-barber-name" placeholder="Ex: João Silva" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="edit-barber-email">E-mail do barbeiro</label>
                    <input type="email" id="edit-barber-email" placeholder="Ex: barbeiro@email.com" autocomplete="off">
                    <small class="input-hint">
                        Esse e-mail será usado futuramente para o login do barbeiro.
                    </small>
                </div>

                <div class="form-group">
                    <label for="edit-barber-services">Serviços relacionados</label>
                    <input type="text" id="edit-barber-services" placeholder="Ex: 1,2,3" autocomplete="off">
                    <small class="input-hint">
                        Informe os IDs dos serviços separados por vírgula. Exemplo: 1,2,3.
                    </small>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancel-edit-button">
                        Cancelar
                    </button>

                    <button type="submit" class="btn-primary" id="save-edit-button">
                        Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal-backdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true">
            <span class="section-tag">Excluir barbeiro</span>
            <h2 class="panel-title">Confirmar exclusão</h2>

            <p class="modal-text">
                Deseja realmente excluir o barbeiro
                <strong id="delete-barber-name">-</strong>?
            </p>

            <div class="modal-warning">
                Se este barbeiro possuir agendamentos, eles serão redirecionados para o próximo barbeiro disponível
                e voltarão para o status pendente.
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancel-delete-button">
                    Cancelar
                </button>

                <button type="button" class="btn-danger-soft" id="confirm-delete-button">
                    Excluir barbeiro
                </button>
            </div>
        </div>
    </div>

    <div id="toast-message" class="toast-message"></div>

    <script>
        const API_BASE = 'index.php';

        const barberForm = document.getElementById('barber-form');
        const barberNameInput = document.getElementById('barber-name');
        const barberEmailInput = document.getElementById('barber-email');
        const barberServicesInput = document.getElementById('barber-services');

        const submitButton = document.getElementById('submit-button');
        const clearButton = document.getElementById('clear-button');
        const reloadButton = document.getElementById('reload-button');
        const searchInput = document.getElementById('search-input');

        const barbersGrid = document.getElementById('barbers-grid');
        const loadingState = document.getElementById('loading-state');
        const toastMessage = document.getElementById('toast-message');

        const editModalBackdrop = document.getElementById('edit-modal-backdrop');
        const editBarberForm = document.getElementById('edit-barber-form');
        const editBarberIdInput = document.getElementById('edit-barber-id');
        const editBarberNameInput = document.getElementById('edit-barber-name');
        const editBarberEmailInput = document.getElementById('edit-barber-email');
        const editBarberServicesInput = document.getElementById('edit-barber-services');
        const cancelEditButton = document.getElementById('cancel-edit-button');
        const saveEditButton = document.getElementById('save-edit-button');

        const deleteModalBackdrop = document.getElementById('delete-modal-backdrop');
        const deleteBarberName = document.getElementById('delete-barber-name');
        const cancelDeleteButton = document.getElementById('cancel-delete-button');
        const confirmDeleteButton = document.getElementById('confirm-delete-button');

        let barbers = [];
        let barberIdToDelete = null;

        function showToast(message) {
            toastMessage.textContent = message;
            toastMessage.classList.add('show');

            setTimeout(() => {
                toastMessage.classList.remove('show');
            }, 2800);
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
            if (!value.trim()) {
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

        function buildDescription(barber) {
            const services = Array.isArray(barber.servicos) ? barber.servicos : [];

            if (services.length > 0) {
                const names = services.map(service => service.nome).join(', ');
                return `Profissional da equipe BarberTime com atendimento voltado para ${names}.`;
            }

            return 'Profissional cadastrado na equipe BarberTime, disponível para novos atendimentos.';
        }

        function getInitials(name) {
            const parts = String(name || 'B')
                .trim()
                .split(' ')
                .filter(Boolean);

            if (parts.length === 0) {
                return 'B';
            }

            if (parts.length === 1) {
                return parts[0].charAt(0).toUpperCase();
            }

            return `${parts[0].charAt(0)}${parts[1].charAt(0)}`.toUpperCase();
        }

        function renderServiceTags(barber) {
            const services = Array.isArray(barber.servicos) ? barber.servicos : [];

            if (services.length === 0) {
                return '<span class="service-tag">Sem serviços cadastrados</span>';
            }

            return services.map(service => {
                return `<span class="service-tag">${escapeHtml(service.nome)}</span>`;
            }).join('');
        }

        function renderBarbers(list) {
            loadingState.style.display = 'none';
            barbersGrid.innerHTML = '';

            if (list.length === 0) {
                barbersGrid.innerHTML = `
                    <div class="empty-state">
                        Nenhum barbeiro encontrado.
                        <br>
                        Cadastre um novo profissional pelo formulário ao lado.
                    </div>
                `;
                return;
            }

            list.forEach((barber) => {
                const card = document.createElement('article');
                card.className = 'barber-card reveal-item is-visible';

                card.innerHTML = `
                    <div class="barber-avatar">${escapeHtml(getInitials(barber.nome))}</div>

                    <h3>${escapeHtml(barber.nome)}</h3>

                    <p class="barber-email">
                        ${barber.email ? escapeHtml(barber.email) : 'E-mail não cadastrado'}
                    </p>

                    <p class="barber-description">
                        ${escapeHtml(buildDescription(barber))}
                    </p>

                    <div class="service-tags">
                        ${renderServiceTags(barber)}
                    </div>

                    <div class="card-actions">
                        <button type="button" class="btn-small" data-action="edit" data-id="${barber.id_barbeiro}">
                            Editar
                        </button>

                        <button type="button" class="btn-danger" data-action="delete" data-id="${barber.id_barbeiro}">
                            Excluir
                        </button>
                    </div>
                `;

                barbersGrid.appendChild(card);
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

        async function loadBarbers() {
            loadingState.style.display = 'block';
            barbersGrid.innerHTML = '';

            try {
                const response = await fetch(`${API_BASE}?action=api_barber_index`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Erro ao carregar barbeiros.');
                    loadingState.style.display = 'none';
                    return;
                }

                barbers = result.data || [];
                filterBarbers();

            } catch (error) {
                console.error(error);
                loadingState.style.display = 'none';
                showToast('Erro ao comunicar com a API.');
            }
        }

        async function saveNewBarber(event) {
            event.preventDefault();

            const nome = barberNameInput.value.trim();
            const email = barberEmailInput.value.trim();
            const servicos = parseServiceIds(barberServicesInput.value);

            if (!nome) {
                showToast('Informe o nome do barbeiro.');
                barberNameInput.focus();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Cadastrando...';

            try {
                const response = await fetch(`${API_BASE}?action=api_barber_store`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nome,
                        email,
                        servicos
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível cadastrar.');
                    return;
                }

                showToast(result.message || 'Barbeiro cadastrado com sucesso.');
                clearCreateForm();
                await loadBarbers();

            } catch (error) {
                console.error(error);
                showToast('Erro ao cadastrar barbeiro.');

            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Cadastrar';
            }
        }

        function openEditModal(id) {
            const barber = barbers.find(item => Number(item.id_barbeiro) === Number(id));

            if (!barber) {
                showToast('Barbeiro não encontrado na lista.');
                return;
            }

            editBarberIdInput.value = barber.id_barbeiro;
            editBarberNameInput.value = barber.nome || '';
            editBarberEmailInput.value = barber.email || '';
            editBarberServicesInput.value = getServiceIdsFromBarber(barber);

            editModalBackdrop.classList.add('show');
            editModalBackdrop.setAttribute('aria-hidden', 'false');

            setTimeout(() => {
                editBarberNameInput.focus();
            }, 150);
        }

        function closeEditModal() {
            editModalBackdrop.classList.remove('show');
            editModalBackdrop.setAttribute('aria-hidden', 'true');

            editBarberIdInput.value = '';
            editBarberNameInput.value = '';
            editBarberEmailInput.value = '';
            editBarberServicesInput.value = '';
        }

        async function saveEditBarber(event) {
            event.preventDefault();

            const id = editBarberIdInput.value;
            const nome = editBarberNameInput.value.trim();
            const email = editBarberEmailInput.value.trim();
            const servicos = parseServiceIds(editBarberServicesInput.value);

            if (!id) {
                showToast('ID do barbeiro não encontrado.');
                return;
            }

            if (!nome) {
                showToast('Informe o nome do barbeiro.');
                editBarberNameInput.focus();
                return;
            }

            saveEditButton.disabled = true;
            saveEditButton.textContent = 'Salvando...';

            try {
                const response = await fetch(`${API_BASE}?action=api_barber_update&id=${encodeURIComponent(id)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nome,
                        email,
                        servicos
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível atualizar.');
                    return;
                }

                showToast(result.message || 'Barbeiro atualizado com sucesso.');
                closeEditModal();
                await loadBarbers();

            } catch (error) {
                console.error(error);
                showToast('Erro ao atualizar barbeiro.');

            } finally {
                saveEditButton.disabled = false;
                saveEditButton.textContent = 'Salvar alterações';
            }
        }

        function openDeleteModal(id) {
            const barber = barbers.find(item => Number(item.id_barbeiro) === Number(id));

            if (!barber) {
                showToast('Barbeiro não encontrado na lista.');
                return;
            }

            barberIdToDelete = id;
            deleteBarberName.textContent = barber.nome || 'este barbeiro';

            deleteModalBackdrop.classList.add('show');
            deleteModalBackdrop.setAttribute('aria-hidden', 'false');
        }

        function closeDeleteModal() {
            barberIdToDelete = null;
            deleteBarberName.textContent = '-';

            deleteModalBackdrop.classList.remove('show');
            deleteModalBackdrop.setAttribute('aria-hidden', 'true');
        }

        async function confirmDeleteBarber() {
            if (!barberIdToDelete) {
                showToast('ID do barbeiro não encontrado.');
                return;
            }

            confirmDeleteButton.disabled = true;
            confirmDeleteButton.textContent = 'Excluindo...';

            try {
                const response = await fetch(`${API_BASE}?action=api_barber_delete&id=${encodeURIComponent(barberIdToDelete)}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || 'Não foi possível excluir.');
                    return;
                }

                showToast(result.message || 'Barbeiro excluído com sucesso.');
                closeDeleteModal();
                await loadBarbers();

            } catch (error) {
                console.error(error);
                showToast('Erro ao excluir barbeiro.');

            } finally {
                confirmDeleteButton.disabled = false;
                confirmDeleteButton.textContent = 'Excluir barbeiro';
            }
        }

        function clearCreateForm() {
            barberNameInput.value = '';
            barberEmailInput.value = '';
            barberServicesInput.value = '';
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
            }, {
                threshold: 0.12
            });

            items.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.04}s`;
                observer.observe(item);
            });
        }

        barberForm.addEventListener('submit', saveNewBarber);
        editBarberForm.addEventListener('submit', saveEditBarber);

        clearButton.addEventListener('click', clearCreateForm);

        reloadButton.addEventListener('click', function () {
            loadBarbers();
            showToast('Lista atualizada.');
        });

        searchInput.addEventListener('input', filterBarbers);

        cancelEditButton.addEventListener('click', closeEditModal);
        cancelDeleteButton.addEventListener('click', closeDeleteModal);
        confirmDeleteButton.addEventListener('click', confirmDeleteBarber);

        editModalBackdrop.addEventListener('click', function (event) {
            if (event.target === editModalBackdrop) {
                closeEditModal();
            }
        });

        deleteModalBackdrop.addEventListener('click', function (event) {
            if (event.target === deleteModalBackdrop) {
                closeDeleteModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                if (editModalBackdrop.classList.contains('show')) {
                    closeEditModal();
                }

                if (deleteModalBackdrop.classList.contains('show')) {
                    closeDeleteModal();
                }
            }
        });

        barbersGrid.addEventListener('click', function (event) {
            const button = event.target.closest('button');

            if (!button) {
                return;
            }

            const action = button.dataset.action;
            const id = button.dataset.id;

            if (action === 'edit') {
                openEditModal(id);
            }

            if (action === 'delete') {
                openDeleteModal(id);
            }
        });

        activateReveals();
        loadBarbers();
    </script>
</body>

</html>