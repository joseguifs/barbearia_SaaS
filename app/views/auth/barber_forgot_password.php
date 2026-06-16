<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha do Barbeiro - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/login-barber.css?v=2">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=barber_login" class="logo">BARBERTIME</a>

    <div class="topbar-pill">
        Recuperação de senha
    </div>
</header>

<main class="page">

    <section class="hero-copy reveal-item">
        <span class="tag">Acesso do barbeiro</span>

        <h1>
            Recupere sua <span>senha</span> de acesso.
        </h1>

        <p>
            Informe o e-mail cadastrado para o barbeiro e defina uma nova senha
            para voltar a acessar seus agendamentos.
        </p>

        <div class="hero-stats">
            <div class="stat-card">
                <strong>🔐</strong>
                <span>Redefinição simples e rápida</span>
            </div>

            <div class="stat-card">
                <strong>💈</strong>
                <span>Acesso ao painel do barbeiro</span>
            </div>
        </div>
    </section>

    <section class="login-card reveal-item">
        <div class="card-header">
            <div class="card-icon">🔐</div>

            <h2>Redefinir senha</h2>

            <p>
                Digite seu e-mail e escolha uma nova senha de acesso.
            </p>
        </div>

        <form id="barberResetPasswordForm">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="barbeiro@email.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="input-group">
                <label for="senha">Nova senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite a nova senha"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="input-group">
                <label for="confirmar_senha">Confirmar senha</label>
                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    placeholder="Confirme a nova senha"
                    autocomplete="new-password"
                    required
                >
            </div>

            <button type="submit" id="resetButton" class="login-button">
                Redefinir senha
            </button>
        </form>

        <div id="result" class="result"></div>

        <div class="login-footer">
            <a href="index.php?action=barber_login" style="color: #c59d5f; text-decoration: none; font-weight: 800;">
                Voltar para o login do barbeiro
            </a>
        </div>
    </section>

</main>

<script>
    const form = document.getElementById('barberResetPasswordForm');
    const result = document.getElementById('result');
    const resetButton = document.getElementById('resetButton');

    function showResult(type, message) {
        result.className = 'result ' + type;
        result.textContent = message;
    }

    function clearResult() {
        result.className = 'result';
        result.textContent = '';
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const email = document.getElementById('email').value.trim();
        const senha = document.getElementById('senha').value.trim();
        const confirmarSenha = document.getElementById('confirmar_senha').value.trim();

        clearResult();

        resetButton.disabled = true;
        resetButton.textContent = 'Redefinindo...';

        try {
            const response = await fetch('index.php?action=api_barber_reset_password', {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    senha: senha,
                    confirmar_senha: confirmarSenha
                })
            });

            let data;

            try {
                data = await response.json();
            } catch (jsonError) {
                showResult('error', 'A API retornou uma resposta inválida.');
                resetButton.disabled = false;
                resetButton.textContent = 'Redefinir senha';
                return;
            }

            if (!response.ok || !data.success) {
                showResult('error', data.message || 'Erro ao redefinir senha.');
                resetButton.disabled = false;
                resetButton.textContent = 'Redefinir senha';
                return;
            }

            showResult('success', data.message || 'Senha redefinida com sucesso.');

            setTimeout(function () {
                window.location.href = data.redirect || 'index.php?action=barber_login';
            }, 1200);

        } catch (error) {
            showResult('error', 'Erro de conexão com a API.');
            resetButton.disabled = false;
            resetButton.textContent = 'Redefinir senha';
        }
    });
</script>

</body>
</html>