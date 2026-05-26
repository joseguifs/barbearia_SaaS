<?php
$errorMessage = $errorMessage ?? null;
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Login - BarberTime</title>
  <link rel="stylesheet" href="/barbearia_SaaS/app/css/login-user.css">

</head>

<body>

  <div class="login-container">

    <section class="login-left">
      <div class="login-content">

        <h1 class="login-title">
          Faça seu login
        </h1>

        <p class="login-subtitle">
          Acesse sua conta para consultar seus horários, criar agendamentos e acompanhar seus atendimentos.
        </p>

        <div
          id="error-box"
          class="error-message <?= !empty($errorMessage) ? 'show' : '' ?>"
        >
          <?= htmlspecialchars($errorMessage ?? '') ?>
        </div>

        <form id="login-form">

          <div class="input-group">
            <label for="email">E-mail</label>

            <div class="input-wrap">
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Digite seu e-mail"
                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                autocomplete="email"
                required
              >
            </div>
          </div>

          <div class="input-group">
            <label for="senha">Senha</label>

            <div class="input-wrap">
              <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Digite sua senha"
                autocomplete="current-password"
                required
              >

              <button
                type="button"
                class="toggle-password"
                data-target="senha"
              >
                Mostrar
              </button>
            </div>
          </div>

          <div class="options">
            <label class="remember">
              <input type="checkbox">
              Lembrar-me
            </label>

            <a href="index.php?action=forgot_password" class="forgot-link">
              Esqueceu a senha?
            </a>
          </div>

          <button type="submit" class="btn-login" id="login-button">
            ENTRAR
          </button>

          <div class="register-text">
            Não tem conta ainda?
            <a href="index.php?action=user_create">
              Crie agora
            </a>
          </div>

        </form>
      </div>
    </section>

    <section class="login-right">
      <div class="right-content">
        <h2>
          Sua barbearia moderna.
        </h2>

        <p>
          Agende horários, acompanhe seus atendimentos e tenha uma experiência premium com o BarberTime.
        </p>
      </div>
    </section>

  </div>

  <script>
    const loginForm = document.getElementById('login-form');
    const loginButton = document.getElementById('login-button');
    const errorBox = document.getElementById('error-box');
    const emailInput = document.getElementById('email');
    const senhaInput = document.getElementById('senha');

    function showError(message) {
      errorBox.textContent = message;
      errorBox.classList.add('show');
    }

    function clearError() {
      errorBox.textContent = '';
      errorBox.classList.remove('show');
    }

    document.querySelectorAll('.toggle-password').forEach(function (button) {
      button.addEventListener('click', function () {
        const targetId = button.getAttribute('data-target');
        const input = document.getElementById(targetId);

        if (!input) {
          return;
        }

        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        button.textContent = isPassword ? 'Ocultar' : 'Mostrar';
      });
    });

    loginForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      clearError();

      const email = emailInput.value.trim().toLowerCase();
      const senha = senhaInput.value.trim();

      if (!email || !senha) {
        showError('Preencha email e senha.');
        return;
      }

      loginButton.disabled = true;
      loginButton.textContent = 'ENTRANDO...';

      try {
        const response = await fetch('index.php?action=api_auth_login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            email,
            senha
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
          showError(result.message || 'Não foi possível realizar o login.');

          loginButton.disabled = false;
          loginButton.textContent = 'ENTRAR';

          return;
        }

        window.location.href = 'index.php?action=home';

      } catch (error) {
        console.error(error);

        showError('Erro ao conectar com a API de autenticação.');

        loginButton.disabled = false;
        loginButton.textContent = 'ENTRAR';
      }
    });
  </script>
</body>
</html>