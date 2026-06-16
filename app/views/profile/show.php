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
    <title>Meu Perfil - BarberTime</title>
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
            <h1>Perfil do usuário</h1>
            <p>Visualize seus dados cadastrados no sistema.</p>
        </section>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert success">
                Perfil atualizado com sucesso.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['delete_error'])): ?>
            <div class="alert error">
                Não foi possível excluir sua conta. Tente novamente.
            </div>
        <?php endif; ?>

        <section class="profile-card">
            <div class="avatar">
                <?= strtoupper(substr(e($cliente['nome']), 0, 1)) ?>
            </div>

            <div class="profile-info">
                <h2><?= e($cliente['nome']) ?></h2>

                <div class="info-grid">
                    <div>
                        <span>ID</span>
                        <strong><?= e($cliente['id_cliente']) ?></strong>
                    </div>

                    <div>
                        <span>Nome</span>
                        <strong><?= e($cliente['nome']) ?></strong>
                    </div>

                    <div>
                        <span>E-mail</span>
                        <strong><?= e($cliente['email']) ?></strong>
                    </div>

                    <div>
                        <span>Telefone</span>
                        <strong><?= e($cliente['telefone'] ?: 'Não informado') ?></strong>
                    </div>
                </div>

                <div class="actions">
                    <a href="index.php?action=profile_edit" class="btn-primary">Editar perfil</a>
                    <a href="index.php?action=home" class="btn-secondary">Voltar</a>

                    <form
                        action="index.php?action=profile_delete"
                        method="POST"
                        class="delete-account-form"
                        onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Todos os seus agendamentos também serão removidos. Essa ação não pode ser desfeita.');"
                    >
                        <button type="submit" class="btn-danger-account">
                            Excluir minha conta
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>