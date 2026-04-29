<?php
$data = $data ?? [];
$errors = $errors ?? [];

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>BarberTime - Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        .register-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            color: #fff;
        }

        .register-box {
            background: rgba(15, 15, 15, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 35px 30px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            text-align: left;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 28px;
        }

        .tab {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.95rem;
            font-weight: bold;
            letter-spacing: 1px;
            padding-bottom: 6px;
            border-bottom: 2px solid transparent;
            transition: 0.3s;
        }

        .tab:hover {
            color: #fff;
        }

        .tab.active {
            color: #e7c48d;
            border-bottom: 2px solid #c59d5f;
        }

        .register-box h2 {
            text-align: center;
            font-size: 1.4rem;
            margin-bottom: 28px;
            color: #ffffff;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.95rem;
        }

        .form-group input {
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

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }

        .form-group input:focus {
            border-bottom-color: #c59d5f;
        }

        .error-text {
            display: block;
            margin-top: 6px;
            font-size: 0.84rem;
            color: #ff8f8f;
        }

        .submit-btn {
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
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            opacity: 0.96;
        }

        .alert {
            margin-top: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.92rem;
            text-align: center;
        }

        .alert.success {
            background: rgba(46, 125, 50, 0.22);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #d4f5d1;
        }

        .alert.error {
            background: rgba(183, 28, 28, 0.18);
            border: 1px solid rgba(244, 67, 54, 0.45);
            color: #ffd3d3;
        }

        @media (max-width: 480px) {
            .register-box {
                padding: 28px 22px;
            }

            .tabs {
                gap: 12px;
            }

            .tab {
                font-size: 0.88rem;
            }
        }
    </style>
</head>
<body>

    <div class="register-wrapper">
        <div class="register-box">
            <div class="tabs">
                <a href="index.php?action=login" class="tab">LOGIN</a>
                <a href="#" class="tab active">REGISTRAR</a>
            </div>

            <h2>Cadastro do Usuário</h2>

            <form action="index.php?action=user_store" method="POST">
                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Digite seu nome completo"
                        value="<?= e($data['nome'] ?? '') ?>"
                    >
                    <?php if (!empty($errors['nome'])): ?>
                        <small class="error-text"><?= e($errors['nome']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        value="<?= e($data['email'] ?? '') ?>"
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <small class="error-text"><?= e($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                    >
                    <?php if (!empty($errors['senha'])): ?>
                        <small class="error-text"><?= e($errors['senha']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        placeholder="Digite seu telefone"
                        value="<?= e($data['telefone'] ?? '') ?>"
                    >
                    <?php if (!empty($errors['telefone'])): ?>
                        <small class="error-text"><?= e($errors['telefone']) ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn">CRIAR CONTA</button>

                <?php if ($success): ?>
                    <div class="alert success">Cadastro realizado com sucesso.</div>
                <?php endif; ?>

                <?php if (!empty($errors['geral'])): ?>
                    <div class="alert error"><?= e($errors['geral']) ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($success): ?>
        <script>
            setTimeout(() => {
                const alerta = document.querySelector('.alert.success');
                if (alerta) {
                    alerta.style.display = 'none';
                }
            }, 3000);

            const url = new URL(window.location.href);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        </script>
    <?php endif; ?>

</body>
</html>