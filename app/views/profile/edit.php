<?php
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
    <title>Editar Perfil - BarberTime</title>
    <link rel="stylesheet" href="../app/css/profile.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">BARBERTIME</div>

        <nav class="nav">
            <a href="index.php?action=home">Início</a>
            <a href="index.php?action=scheduling_create">Agendar</a>
            <a href="index.php?action=scheduling_get">Meus Agendamentos</a>
            <a href="index.php?action=profile" class="active">Perfil</a>
             <a href="index.php?action=scheduling_history">Histórico</a>
            <a href="index.php?action=logout">Sair</a>
        </nav>
    </header>

    <main class="profile-page">
        <section class="hero-profile">
            <span>Minha conta</span>
            <h1>Editar perfil</h1>
            <p>Atualize suas informações pessoais.</p>
        </section>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <strong>Corrija os erros:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="profile-card form-card">
            <form action="index.php?action=profile_update" method="POST">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?= e($cliente['nome']) ?>" required>
                    <small>Não pode existir outro usuário com o mesmo nome.</small>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= e($cliente['email']) ?>" required>
                    <small>Não pode existir outro usuário com o mesmo e-mail.</small>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?= e($cliente['telefone']) ?>">
                </div>

                <div class="actions">
                    <button type="submit" class="btn-primary">Salvar alterações</button>
                    <a href="index.php?action=profile" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>