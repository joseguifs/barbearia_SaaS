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

    <link rel="stylesheet" href="../app/css/user.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="page">
        <div class="frame">
            <header class="brand">BARBERTIME</header>

            <div class="content">
                <section class="image-side"></section>

                <section class="form-side">
                    <div class="form-box">
                        <div class="tabs">
                            <a href="#" class="tab">LOGIN</a>
                            <a href="#" class="tab active">REGISTRAR</a>
                        </div>


                        <form class="register-form" action="index.php?action=user_store" method="POST">
                            <div>
                                <div class="input-group">
                                    <i class="fa-regular fa-user"></i>
                                    <input type="text" name="nome" placeholder="NOME COMPLETO"
                                        value="<?= e($data['nome'] ?? '') ?>">
                                </div>
                                <?php if (!empty($errors['nome'])): ?>
                                    <small class="error-text"><?= e($errors['nome']) ?></small>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="input-group">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="email" name="email" placeholder="ENDEREÇO EMAIL"
                                        value="<?= e($data['email'] ?? '') ?>">
                                </div>
                                <?php if (!empty($errors['email'])): ?>
                                    <small class="error-text"><?= e($errors['email']) ?></small>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="input-group">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" name="senha" placeholder="SENHA">
                                </div>
                                <?php if (!empty($errors['senha'])): ?>
                                    <small class="error-text"><?= e($errors['senha']) ?></small>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="input-group">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="text" name="telefone" placeholder="TELEFONE"
                                        value="<?= e($data['telefone'] ?? '') ?>">
                                </div>
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
                </section>
            </div>
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