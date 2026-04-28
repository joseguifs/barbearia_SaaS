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
    <title>Login - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/login.css">
</head>
<body>
    <main class="auth-page">
        <div class="auth-overlay"></div>

        <section class="auth-card">
            <h1 class="auth-title">Login do Usuário</h1>

            <?php if (!empty($errors['geral'])): ?>
                <div class="alert error"><?= e($errors['geral']) ?></div>
            <?php endif; ?>

            <form action="index.php?action=authenticate" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        value="<?= e($data['email'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                    >
                </div>

                <div class="login-options">
                    <label class="remember">
                        <input type="checkbox" name="lembrar">
                        <span>Lembrar-me</span>
                    </label>

                    <a href="index.php?action=forgot_password" class="forgot-password">Esqueceu a senha?</a>
                </div>

                <button type="submit" class="submit-btn">ENTRAR</button>
            </form>
        </section>
    </main>
</body>
</html>