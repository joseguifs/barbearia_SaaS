<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login do Barbeiro - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/login-barber.css">

</head>

<body>

    <header class="topbar">
        <a href="index.php?action=barber_login" class="logo">BARBERTIME</a>

        <div class="topbar-pill">
            Área exclusiva do barbeiro
        </div>
    </header>

    <main class="page">

        <section class="hero-copy reveal-item">
            <span class="tag">Painel de gestão</span>

            <h1>
                Gerencie seus <span>agendamentos</span> com praticidade.
            </h1>

            <p>
                Acesse sua conta para visualizar solicitações pendentes, confirmar horários
                e acompanhar apenas os atendimentos vinculados ao seu perfil.
            </p>

            <div class="hero-stats">
                <div class="stat-card">
                    <strong>✓</strong>
                    <span>Confirmação rápida de agendamentos</span>
                </div>

                <div class="stat-card">
                    <strong>💈</strong>
                    <span>Área individual para cada barbeiro</span>
                </div>
            </div>
        </section>

        <section class="login-card reveal-item">
            <div class="card-header">
                <div class="card-icon">✂</div>

                <h2>Login do barbeiro</h2>

                <p>
                    Entre com seu e-mail e senha para acessar seus agendamentos pendentes.
                </p>
            </div>

            <form id="barberLoginForm">
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
                    Entrar como barbeiro
                </button>
            </form>

            <div id="result" class="result"></div>

            <div class="login-footer">
                Após o login, você será direcionado para seus agendamentos pendentes.
            </div>
        </section>

    </main>

    <script>
        const form = document.getElementById('barberLoginForm');
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
                const response = await fetch('index.php?action=api_barber_login', {
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

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showResult('error', data.message || 'Erro ao realizar login.');
                    loginButton.disabled = false;
                    loginButton.textContent = 'Entrar como barbeiro';
                    return;
                }

                window.location.href = data.redirect || 'index.php?action=review_pending';

            } catch (error) {
                showResult('error', 'Erro de conexão com a API.');
                loginButton.disabled = false;
                loginButton.textContent = 'Entrar como barbeiro';
            }
        });
    </script>

</body>
</html>