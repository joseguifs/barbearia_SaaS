<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin.css">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=admin_login" class="logo">BARBERTIME</a>

    <div class="topbar-pill">
        Área administrativa
    </div>
</header>

<main class="page">
    <section class="hero-copy reveal-item">
        <span class="tag">Painel administrativo</span>

        <h1>
            Controle geral do <span>BarberTime</span>.
        </h1>

        <p>
            Acesse a área administrativa para gerenciar usuários, agendamentos,
            barbeiros e serviços da plataforma.
        </p>

        <div class="hero-stats">
            <div class="stat-card">
                <strong>ADM</strong>
                <span>Permissão total do sistema</span>
            </div>

            <div class="stat-card">
                <strong>⚙</strong>
                <span>Gestão centralizada da barbearia</span>
            </div>
        </div>
    </section>

    <section class="login-card reveal-item">
        <div class="card-header">
            <div class="card-icon">⚙</div>

            <h2>Login do admin</h2>

            <p>
                Entre com o e-mail e senha do administrador geral.
            </p>
        </div>

        <form id="adminLoginForm">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="admin@barbertime.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="input-group">
                <label for="senha">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" id="loginButton" class="login-button">
                Entrar como admin
            </button>
        </form>

        <div id="result" class="result"></div>

        <div class="login-footer">
            Após o login, você será direcionado para a home administrativa.
        </div>
    </section>
</main>

<script>
    const form = document.getElementById('adminLoginForm');
    const result = document.getElementById('result');
    const loginButton = document.getElementById('loginButton');

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

        clearResult();

        loginButton.disabled = true;
        loginButton.textContent = 'Entrando...';

        try {
            const response = await fetch('index.php?action=api_admin_login', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    senha: senha
                })
            });

            let data;

            try {
                data = await response.json();
            } catch (jsonError) {
                showResult('error', 'A API retornou uma resposta inválida.');
                loginButton.disabled = false;
                loginButton.textContent = 'Entrar como admin';
                return;
            }

            if (!response.ok || !data.success) {
                showResult('error', data.message || 'Erro ao realizar login.');
                loginButton.disabled = false;
                loginButton.textContent = 'Entrar como admin';
                return;
            }

            window.location.href = data.redirect || 'index.php?action=admin_home';

        } catch (error) {
            showResult('error', 'Erro de conexão com a API.');
            loginButton.disabled = false;
            loginButton.textContent = 'Entrar como admin';
        }
    });
</script>

</body>
</html>