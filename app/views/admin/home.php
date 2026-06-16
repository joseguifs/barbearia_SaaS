<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$adminNome = $_SESSION['admin_nome'] ?? 'Administrador';

$cards = [
    [
        'label' => 'Clientes',
        'value' => $stats['clientes'] ?? 0,
        'description' => 'Usuários cadastrados na plataforma.'
    ],
    [
        'label' => 'Barbeiros',
        'value' => $stats['barbeiros'] ?? 0,
        'description' => 'Profissionais cadastrados no sistema.'
    ],
    [
        'label' => 'Serviços',
        'value' => $stats['servicos'] ?? 0,
        'description' => 'Serviços disponíveis para agendamento.'
    ],
    [
        'label' => 'Agendamentos',
        'value' => $stats['agendamentos'] ?? 0,
        'description' => 'Total de agendamentos registrados.'
    ],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Home Admin - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin-home.css">
</head>

<body>

<header class="topbar">
    <a href="index.php?action=admin_home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=admin_home" class="active">Home Admin</a>
        <a href="index.php?action=admin_schedulings">Agendamentos</a>
        <a href="index.php?action=admin_clients">Usuários</a>
        <a href="index.php?action=admin_barbers">Barbeiros</a>
        <a href="index.php?action=admin_services">Serviços</a>

        <form
            action="index.php?action=admin_logout"
            method="POST"
            class="logout-form"
            onsubmit="return confirm('Deseja sair da conta de administrador?');"
        >
            <button type="submit" class="btn-logout">
                Sair
            </button>
        </form>
    </nav>
</header>

<main class="page">
    <section class="admin-container">

        <aside class="admin-side">
            <span class="side-tag">Administrador geral</span>

            <h1>Painel central do BarberTime</h1>

            <p>
                Gerencie usuários, agendamentos, barbeiros e serviços a partir de uma única área administrativa.
            </p>

            <div class="manager-box">
                <div class="manager-avatar">
                    <?= e(strtoupper(substr($adminNome, 0, 1))) ?>
                </div>

                <div>
                    <span>Admin logado</span>
                    <strong><?= e($adminNome) ?></strong>
                </div>
            </div>

            <div class="info-box">
                <span>Status do sistema</span>
                <strong>Operacional</strong>
                <p>
                    Use os atalhos ao lado para acessar as áreas de gerenciamento.
                </p>
            </div>
        </aside>

        <section class="admin-content">
            <div class="content-header">
                <span>Resumo geral</span>
                <h2>Visão administrativa</h2>
                <p>
                    Acompanhe os principais números do sistema e acesse rapidamente os módulos de gestão.
                </p>
            </div>

            <section class="stats-grid">
                <?php foreach ($cards as $card): ?>
                    <div class="stat-card">
                        <span><?= e($card['label']) ?></span>
                        <strong><?= e($card['value']) ?></strong>
                        <p><?= e($card['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="status-grid">
                <div class="status-card">
                    <span>Pendentes</span>
                    <strong><?= e($stats['pendentes'] ?? 0) ?></strong>
                    <p>Agendamentos aguardando confirmação.</p>
                </div>

                <div class="status-card">
                    <span>Agendados</span>
                    <strong><?= e($stats['agendados'] ?? 0) ?></strong>
                    <p>Agendamentos confirmados.</p>
                </div>

                <div class="status-card">
                    <span>Cancelados</span>
                    <strong><?= e($stats['cancelados'] ?? 0) ?></strong>
                    <p>Agendamentos recusados ou cancelados.</p>
                </div>

                <div class="status-card">
                    <span>Hoje</span>
                    <strong><?= e($stats['hoje'] ?? 0) ?></strong>
                    <p>Agendamentos marcados para hoje.</p>
                </div>
            </section>

            <section class="shortcut-grid">
                <a href="index.php?action=admin_schedulings" class="shortcut-card">
                    <span>📅</span>
                    <strong>Gerenciar agendamentos</strong>
                    <p>Aceitar, recusar, editar, visualizar e excluir agendamentos.</p>
                </a>

                <a href="index.php?action=admin_clients" class="shortcut-card">
                    <span>👤</span>
                    <strong>Gerenciar usuários</strong>
                    <p>Visualizar, editar e remover clientes cadastrados.</p>
                </a>

                <a href="index.php?action=admin_barbers" class="shortcut-card">
                    <span>💈</span>
                    <strong>Gerenciar barbeiros</strong>
                    <p>Cadastrar e administrar barbeiros da plataforma.</p>
                </a>

                <a href="index.php?action=admin_services" class="shortcut-card">
                    <span>✂</span>
                    <strong>Gerenciar serviços</strong>
                    <p>Cadastrar e editar serviços oferecidos pela barbearia.</p>
                </a>
            </section>
        </section>

    </section>
</main>

</body>
</html>