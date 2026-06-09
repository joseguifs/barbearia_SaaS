<?php
$errors = $errors ?? [];
$data = $data ?? [];

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
    <title>Esqueci a Senha - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <link rel="stylesheet" href="/barbearia_SaaS/app/css/forgot-password.css">


</head>
<body>

    <div class="forgot-wrapper">
        <section class="forgot-box" id="forgot-box">

            <div class="floating-glow"></div>

            <h1>Recuperar Senha</h1>

            <p class="forgot-description">
                Informe o e-mail cadastrado para continuar o processo.
            </p>

            <form action="index.php?action=forgot_password_submit" method="POST" id="forgot-form">
                <input type="hidden" name="role" value="<?= e($data['role'] ?? 'client') ?>">

                <div class="form-group">
                    <label for="email">E-mail</label>

                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            value="<?= e($data['email'] ?? '') ?>"
                            autocomplete="email"
                        >
                    </div>

                    <small class="helper-text">
                        Você receberá acesso para redefinir sua senha.
                    </small>

                    <?php if (!empty($errors['email'])): ?>
                        <small class="error-text"><?= e($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">
                    CONTINUAR
                </button>

                <div class="actions">
                    <?php if (($data['role'] ?? 'client') === 'barber'): ?>
                        <a href="index.php?action=barber_login" class="action-link login">
                            VOLTAR AO LOGIN
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=login" class="action-link login">
                            VOLTAR AO LOGIN
                        </a>

                        <a href="index.php?action=user_create" class="action-link register">
                            CRIAR CONTA
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>

    <script>
        const forgotBox = document.getElementById('forgot-box');
        const emailInput = document.getElementById('email');
        const form = document.getElementById('forgot-form');
        const submitBtn = document.getElementById('submit-btn');

        window.addEventListener('load', () => {
            setTimeout(() => {
                forgotBox.classList.add('show');
            }, 120);
        });

        const wrap = emailInput.closest('.input-wrap');

        emailInput.addEventListener('focus', () => {
            wrap.classList.add('is-focused');
        });

        emailInput.addEventListener('blur', () => {
            wrap.classList.remove('is-focused');
            emailInput.value = emailInput.value.trim().toLowerCase();
        });

        form.addEventListener('submit', () => {
            submitBtn.textContent = 'PROCESSANDO...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        });

        document.addEventListener('mousemove', (e) => {
            const glow = document.querySelector('.floating-glow');

            const moveX = (e.clientX / window.innerWidth) * 10;
            const moveY = (e.clientY / window.innerHeight) * 10;

            glow.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    </script>

</body>
</html>