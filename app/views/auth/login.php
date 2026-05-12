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

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }

    body {
      min-height: 100vh;
      background: #0f0f0f;
      overflow: hidden;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;

      background:
        linear-gradient(
          rgba(0, 0, 0, 0.25),
          rgba(0, 0, 0, 0.55)
        ),
        url("assets/images/backgroundLogin.jpeg")
        center center / cover no-repeat;

      transform: scale(1.08);

      filter:
        blur(2px)
        brightness(0.46);

      z-index: -2;
    }

    body::after {
      content: "";
      position: fixed;
      inset: 0;

      background:
        radial-gradient(circle at top right, rgba(197, 157, 95, 0.14), transparent 35%),
        linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.60));

      z-index: -1;
    }

    .login-container {
      width: 100%;
      min-height: 100vh;
      display: flex;
    }

    .login-left {
      width: 60%;

      background: 
        linear-gradient(
          135deg,
          rgba(18, 18, 18, 0.72),
          rgba(18, 18, 18, 0.52),
          rgba(197, 157, 95, 0.08)
        );

      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);

      border-right: 1px solid rgba(197, 157, 95, 0.16);

      display: flex;
      align-items: center;
      justify-content: center;

      padding: 50px;

      position: relative;
      z-index: 2;

      box-shadow: 20px 0 60px rgba(0, 0, 0, 0.30);
    }

    .login-content {
      width: 100%;
      max-width: 500px;
      animation: fadeInLeft 0.8s ease;
    }

    .login-title {
      font-size: 3rem;
      font-weight: bold;
      color: #fff;
      margin-bottom: 14px;
      line-height: 1.1;
    }

    .login-subtitle {
      color: rgba(255, 255, 255, 0.72);
      line-height: 1.6;
      margin-bottom: 34px;
      font-size: 1rem;
    }

    .login-title::after {
      content: "";
      display: block;
      width: 58px;
      height: 4px;
      margin-top: 14px;
      border-radius: 999px;
      background: linear-gradient(135deg, #c59d5f, #8b5e34);
    }

    .error-message {
      margin-bottom: 20px;
      padding: 14px;
      border-radius: 14px;
      background: rgba(183, 28, 28, 0.20);
      border: 1px solid rgba(183, 28, 28, 0.55);
      color: #ffdada;
      font-size: 0.95rem;
      display: none;
    }

    .error-message.show {
      display: block;
      animation: shake 0.35s ease;
    }

    .input-group {
      margin-bottom: 24px;
      width: 100%;
    }

    .input-group label {
      display: block;
      margin-bottom: 8px;
      color: rgba(255, 255, 255, 0.88);
      font-size: 0.95rem;
      font-weight: 600;
    }

    .input-wrap {
      position: relative;
      width: 100%;
    }

    .input-wrap input {
      width: 100%;
      height: 56px;

      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 14px;

      padding: 0 52px 0 16px;

      font-size: 1rem;
      outline: none;

      transition: 0.25s ease;

      background: rgba(255, 255, 255, 0.06);
      color: #fff;

      display: block;
    }

    .input-wrap input:focus {
      border-color: #c59d5f;
      box-shadow: 0 0 0 4px rgba(197, 157, 95, 0.12);
      background: rgba(255, 255, 255, 0.08);
    }

    .input-wrap input::placeholder {
      color: rgba(255, 255, 255, 0.45);
    }

    .toggle-password {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);

      background: transparent;
      border: none;

      cursor: pointer;

      color: rgba(255, 255, 255, 0.62);
      font-size: 0.9rem;

      transition: 0.2s;
    }

    .toggle-password:hover {
      color: #c59d5f;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;

      margin-bottom: 28px;

      gap: 10px;
      flex-wrap: wrap;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 8px;

      color: rgba(255, 255, 255, 0.72);
      font-size: 0.94rem;
    }

    .remember input {
      accent-color: #c59d5f;
    }

    .forgot-link {
      color: #c59d5f;
      text-decoration: none;
      font-size: 0.94rem;
      font-weight: 600;
      transition: 0.2s;
    }

    .forgot-link:hover {
      color: #fff;
    }

    .btn-login {
      width: 100%;
      height: 56px;

      border: none;
      border-radius: 14px;

      background: linear-gradient(135deg, #c59d5f, #8b5e34);

      color: #fff;

      font-size: 1rem;
      font-weight: bold;
      letter-spacing: 0.5px;

      cursor: pointer;

      transition: 0.25s ease;

      margin-bottom: 24px;
      box-shadow: 0 14px 30px rgba(0, 0, 0, 0.25);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      opacity: 0.96;
    }

    .btn-login:disabled {
      opacity: 0.7;
      cursor: wait;
      transform: none;
    }

    .register-text {
      text-align: center;
      color: rgba(255, 255, 255, 0.72);
      font-size: 0.95rem;
    }

    .register-text a {
      color: #c59d5f;
      text-decoration: none;
      font-weight: bold;
    }

    .register-text a:hover {
      text-decoration: underline;
    }

    .login-right {
      width: 40%;
      position: relative;
      overflow: hidden;

      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-right::before {
      content: "";
      position: absolute;
      inset: 0;

      background:
        linear-gradient(
          rgba(0, 0, 0, 0.45),
          rgba(0, 0, 0, 0.55)
        ),
        url("assets/images/backgroundLogin.jpeg")
        center center / cover no-repeat;

      transform: scale(1.05);

      animation: zoomBg 12s ease-in-out infinite alternate;
    }

    .right-content {
      position: relative;
      z-index: 2;

      text-align: center;
      color: #fff;

      padding: 40px;

      animation: fadeInRight 1s ease;
    }

    .right-content h2 {
      font-size: 2.2rem;
      margin-bottom: 18px;
      line-height: 1.2;
    }

    .right-content p {
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.85);

      max-width: 500px;
      line-height: 1.7;
    }

    @keyframes fadeInLeft {
      from {
        opacity: 0;
        transform: translateX(-35px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes fadeInRight {
      from {
        opacity: 0;
        transform: translateX(35px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes zoomBg {
      from {
        transform: scale(1.05);
      }

      to {
        transform: scale(1.12);
      }
    }

    @keyframes shake {
      0% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-4px);
      }

      50% {
        transform: translateX(4px);
      }

      75% {
        transform: translateX(-4px);
      }

      100% {
        transform: translateX(0);
      }
    }

    @media (max-width: 980px) {
      .login-right {
        display: none;
      }

      .login-left {
        width: 100%;
      }

      .login-title {
        font-size: 2.3rem;
      }
    }

    @media (max-width: 480px) {
      .login-left {
        padding: 30px 22px;
      }

      .login-title {
        font-size: 2rem;
      }

      .options {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
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