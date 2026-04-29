<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Barbearia</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }

    body {
      min-height: 100vh;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: #0f0f0f;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url('assets/images/backgroundLogin.jpeg') center center/cover no-repeat;
      transform: scale(1.08);
      filter: blur(4px) brightness(0.6);
    }

    body::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(rgba(0, 0, 0, 0.28), rgba(0, 0, 0, 0.5));
    }

    .login-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 430px;
      text-align: center;
      color: #fff;
    }

    .brand-title {
      font-size: 2.2rem;
      font-weight: 300;
      margin-bottom: 10px;
      letter-spacing: 1px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    }

    .brand-subtitle {
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 28px;
    }

    .login-box {
      background: rgba(15, 15, 15, 0.45);
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 18px;
      padding: 35px 30px;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
      text-align: left;
    }

    .login-box h2 {
      text-align: center;
      font-size: 1.4rem;
      margin-bottom: 28px;
      color: #ffffff;
      font-weight: 600;
    }

    .input-group {
      margin-bottom: 22px;
    }

    .input-group label {
      display: block;
      margin-bottom: 8px;
      color: rgba(255, 255, 255, 0.92);
      font-size: 0.95rem;
    }

    .input-group input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid rgba(255, 255, 255, 0.5);
      outline: none;
      padding: 10px 2px;
      color: #fff;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .input-group input::placeholder {
      color: rgba(255, 255, 255, 0.55);
    }

    .input-group input:focus {
      border-bottom-color: #c59d5f;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      margin: 8px 0 24px;
      font-size: 0.92rem;
      color: rgba(255, 255, 255, 0.9);
      flex-wrap: wrap;
    }

    .options a {
      color: #e7c48d;
      text-decoration: none;
      transition: 0.3s;
    }

    .options a:hover {
      color: #fff;
    }

    .btn-login {
      width: 100%;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #c59d5f, #8b5e34);
      color: #fff;
      padding: 14px;
      font-size: 1rem;
      font-weight: bold;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      opacity: 0.96;
    }

    .footer-text {
      margin-top: 20px;
      font-size: 0.82rem;
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
    }

    @media (max-width: 480px) {
      .brand-title {
        font-size: 1.8rem;
      }

      .login-box {
        padding: 28px 22px;
      }

      .options {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-box">
      <h2>Login do Usuário</h2>

      <form action="#" method="post">
        <div class="input-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
        </div>

        <div class="input-group">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
        </div>

        <div class="options">
          <label>
            <input type="checkbox"> Lembrar-me
          </label>
          <a href="#">Esqueceu a senha?</a>
        </div>

        <button type="submit" class="btn-login">ENTRAR</button>
      </form>
    </div>

  </div>
</body>
</html>
