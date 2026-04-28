<?php
function e($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

$old = $old ?? [];
$errorMessage = $errorMessage ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberTime</title>
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <main class="page">
        <section class="frame">
            <div class="brand">Barbertime</div>

            <div class="content">
                <div class="image-side"></div>

                <div class="form-side">
                    <div class="form-box">
                        <div class="tabs">
                            <button type="button" class="tab active">LOGIN</button>
                            <button type="button" class="tab" onclick="window.location.href='index.php?action=client_create'">REGISTRAR</button>
                        </div>

                        <form action="index.php?action=authenticate" method="POST" class="register-form">
                            <div class="input-group">
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="ENDEREÇO EMAIL"
                                    value="<?= e($old['email'] ?? '') ?>"
                                    required
                                >
                            </div>

                            <div class="input-group">
                                <input
                                    type="password"
                                    name="senha"
                                    placeholder="SENHA"
                                    required
                                >
                            </div>

                            <button type="submit" class="submit-btn">ENTRAR</button>

                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert error">
                                    <?= e($errorMessage) ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>