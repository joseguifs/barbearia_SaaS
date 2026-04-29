<?php
$errors = $errors ?? [];
$email = $_GET['email'] ?? '';

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
    <title>Redefinir Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/login.css">
</head>
<body>
    <main class="auth-page">
        <div class="auth-overlay"></div>

        <section class="auth-card">
            <h1 class="auth-title">Redefinir Senha</h1>

            <?php if (!empty($errors['geral'])): ?>
                <div class="alert error"><?= e($errors['geral']) ?></div>
            <?php endif; ?>

            <form action="index.php?action=reset_password" method="POST" class="auth-form">
                <input type="hidden" name="email" value="<?= e($email) ?>">

                <div class="form-group">
                    <label for="senha">Nova senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite a nova senha">
                    <?php if (!empty($errors['senha'])): ?>
                        <small class="error-text"><?= e($errors['senha']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite novamente a senha">
                    <?php if (!empty($errors['confirmar_senha'])): ?>
                        <small class="error-text"><?= e($errors['confirmar_senha']) ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn">REDEFINIR SENHA</button>
            </form>
        </section>
    </main>
</body>
</html>