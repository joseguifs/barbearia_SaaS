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
    <title>Esqueci a Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/login.css">
</head>
<body>
    <main class="auth-page">
        <div class="auth-overlay"></div>

        <section class="auth-card">
            <h1 class="auth-title">Esqueci a Senha</h1>

            <form action="index.php?action=forgot_password_submit" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" value="<?= e($data['email'] ?? '') ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <small class="error-text"><?= e($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn">CONTINUAR</button>
            </form>
        </section>
    </main>
</body>
</html>