<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$adminNome = $_SESSION['admin_nome'] ?? 'Administrador';
$message = $_GET['message'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin-manage.css">
</head>
<body>

<header class="topbar">
    <a href="index.php?action=admin_home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=admin_home">Home Admin</a>
        <a href="index.php?action=admin_schedulings">Agendamentos</a>
        <a href="index.php?action=admin_clients" class="active">Usuários</a>
        <a href="index.php?action=admin_barbers">Barbeiros</a>
        <a href="index.php?action=admin_services">Serviços</a>

        <form action="index.php?action=admin_logout" method="POST" class="logout-form">
            <button type="submit" class="btn-logout">Sair</button>
        </form>
    </nav>
</header>

<main class="page">
    <section class="admin-container">

        <aside class="admin-side">
            <span class="side-tag">Usuários</span>

            <h1>Gerenciar clientes</h1>

            <p>
                Cadastre, edite e remova usuários/clientes da plataforma BarberTime.
            </p>

            <div class="manager-box">
                <div class="manager-avatar"><?= e(strtoupper(substr($adminNome, 0, 1))) ?></div>

                <div>
                    <span>Admin logado</span>
                    <strong><?= e($adminNome) ?></strong>
                </div>
            </div>
        </aside>

        <section class="admin-content">
            <div class="content-header">
                <span>Controle de usuários</span>
                <h2>Clientes cadastrados</h2>
                <p>Gerencie os dados dos clientes que utilizam o sistema.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert">
                    Operação realizada: <?= e($message) ?>
                </div>
            <?php endif; ?>

            <form method="GET" class="filter-card">
                <input type="hidden" name="action" value="admin_clients">

                <input 
                    type="text" 
                    name="busca" 
                    placeholder="Buscar por nome, e-mail ou telefone..."
                    value="<?= e($_GET['busca'] ?? '') ?>"
                >

                <button type="submit" class="btn-main">Filtrar</button>
            </form>

            <details class="create-card">
                <summary>Novo usuário</summary>

                <form action="index.php?action=admin_client_store" method="POST" class="form-grid">
                    <input type="text" name="nome" placeholder="Nome do cliente" required>
                    <input type="text" name="telefone" placeholder="Telefone">
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="password" name="senha" placeholder="Senha de acesso" required>

                    <button type="submit" class="btn-main full">Cadastrar usuário</button>
                </form>
            </details>

            <div class="list-grid">
                <?php if (empty($clientes)): ?>
                    <div class="empty-card">
                        Nenhum usuário encontrado.
                    </div>
                <?php endif; ?>

                <?php foreach ($clientes as $cliente): ?>
                    <article class="manage-card">
                        <div class="card-title">
                            <div>
                                <span>#<?= (int) $cliente['id_cliente'] ?></span>
                                <h3><?= e($cliente['nome']) ?></h3>
                                <p><?= e($cliente['email']) ?> • <?= e($cliente['telefone'] ?? 'Sem telefone') ?></p>
                            </div>
                        </div>

                        <form action="index.php?action=admin_client_update" method="POST" class="form-grid">
                            <input type="hidden" name="id_cliente" value="<?= (int) $cliente['id_cliente'] ?>">

                            <input type="text" name="nome" value="<?= e($cliente['nome']) ?>" required>
                            <input type="text" name="telefone" value="<?= e($cliente['telefone'] ?? '') ?>">
                            <input type="email" name="email" value="<?= e($cliente['email']) ?>" required>
                            <input type="password" name="senha" placeholder="Nova senha opcional">

                            <button type="submit" class="btn-main">Salvar alterações</button>
                        </form>

                        <form 
                            action="index.php?action=admin_client_delete" 
                            method="POST"
                            onsubmit="return confirm('Deseja excluir este usuário?');"
                        >
                            <input type="hidden" name="id_cliente" value="<?= (int) $cliente['id_cliente'] ?>">
                            <button type="submit" class="btn-danger">Excluir usuário</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </section>
</main>

</body>
</html>