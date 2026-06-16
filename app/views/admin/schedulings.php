<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$adminNome = $_SESSION['admin_nome'] ?? 'Administrador';
$message = $_GET['message'] ?? null;

function selected($value, $current)
{
    return (string) $value === (string) $current ? 'selected' : '';
}

function checkedService($idServico, $ids)
{
    return in_array((string) $idServico, $ids, true) ? 'selected' : '';
}

function formatDateTimeLocal($value)
{
    if (empty($value)) {
        return '';
    }

    return date('Y-m-d\TH:i', strtotime($value));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Agendamentos - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/barbearia_SaaS/app/css/admin-manage.css">
</head>
<body>

<header class="topbar">
    <a href="index.php?action=admin_home" class="logo">BARBERTIME</a>

    <nav class="navbar">
        <a href="index.php?action=admin_home">Home Admin</a>
        <a href="index.php?action=admin_schedulings" class="active">Agendamentos</a>
        <a href="index.php?action=admin_clients">Usuários</a>
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
            <span class="side-tag">Agendamentos</span>

            <h1>Gerenciar agendamentos</h1>

            <p>
                Crie, edite, filtre, atualize status e remova agendamentos cadastrados no sistema.
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
                <span>Controle geral</span>
                <h2>Agendamentos cadastrados</h2>
                <p>Use os filtros para localizar agendamentos por status, data, cliente ou barbeiro.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert">
                    Operação realizada: <?= e($message) ?>
                </div>
            <?php endif; ?>

            <form method="GET" class="filter-card">
                <input type="hidden" name="action" value="admin_schedulings">

                <input 
                    type="text" 
                    name="busca" 
                    placeholder="Buscar por cliente, e-mail, barbeiro..."
                    value="<?= e($_GET['busca'] ?? '') ?>"
                >

                <input 
                    type="date" 
                    name="data"
                    value="<?= e($_GET['data'] ?? '') ?>"
                >

                <select name="status">
                    <option value="">Todos os status</option>
                    <option value="pendente" <?= selected('pendente', $_GET['status'] ?? '') ?>>Pendente</option>
                    <option value="agendado" <?= selected('agendado', $_GET['status'] ?? '') ?>>Agendado</option>
                    <option value="cancelado" <?= selected('cancelado', $_GET['status'] ?? '') ?>>Cancelado</option>
                </select>

                <button type="submit" class="btn-main">Filtrar</button>
            </form>

            <details class="create-card">
                <summary>Novo agendamento</summary>

                <form action="index.php?action=admin_scheduling_store" method="POST" class="form-grid">
                    <select name="id_cliente" required>
                        <option value="">Selecione o cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= (int) $cliente['id_cliente'] ?>">
                                <?= e($cliente['nome']) ?> - <?= e($cliente['email']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="id_barbeiro" required>
                        <option value="">Selecione o barbeiro</option>
                        <?php foreach ($barbeiros as $barbeiro): ?>
                            <option value="<?= (int) $barbeiro['id_barbeiro'] ?>">
                                <?= e($barbeiro['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="datetime-local" name="data_hora" required>

                    <select name="status">
                        <option value="pendente">Pendente</option>
                        <option value="agendado">Agendado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>

                    <select name="servicos[]" multiple class="full">
                        <?php foreach ($servicos as $servico): ?>
                            <option value="<?= (int) $servico['id_servico'] ?>">
                                <?= e($servico['nome']) ?> - R$ <?= e($servico['preco']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <textarea name="descricao" class="full" placeholder="Observações do agendamento"></textarea>

                    <button type="submit" class="btn-main full">Cadastrar agendamento</button>
                </form>
            </details>

            <div class="list-grid">
                <?php if (empty($agendamentos)): ?>
                    <div class="empty-card">
                        Nenhum agendamento encontrado.
                    </div>
                <?php endif; ?>

                <?php foreach ($agendamentos as $agendamento): ?>
                    <?php
                        $selectedServices = !empty($agendamento['servicos_ids'])
                            ? explode(',', $agendamento['servicos_ids'])
                            : [];
                    ?>

                    <article class="manage-card">
                        <div class="card-title">
                            <div>
                                <span>#<?= (int) $agendamento['id_agendamento'] ?></span>
                                <h3><?= e($agendamento['cliente_nome']) ?></h3>
                                <p><?= e($agendamento['barbeiro_nome']) ?> • <?= e($agendamento['servicos']) ?></p>
                            </div>

                            <strong class="status-pill"><?= e($agendamento['status']) ?></strong>
                        </div>

                        <form action="index.php?action=admin_scheduling_update" method="POST" class="form-grid">
                            <input type="hidden" name="id_agendamento" value="<?= (int) $agendamento['id_agendamento'] ?>">

                            <select name="id_cliente" required>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option 
                                        value="<?= (int) $cliente['id_cliente'] ?>"
                                        <?= selected($cliente['id_cliente'], $agendamento['id_cliente']) ?>
                                    >
                                        <?= e($cliente['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="id_barbeiro" required>
                                <?php foreach ($barbeiros as $barbeiro): ?>
                                    <option 
                                        value="<?= (int) $barbeiro['id_barbeiro'] ?>"
                                        <?= selected($barbeiro['id_barbeiro'], $agendamento['id_barbeiro']) ?>
                                    >
                                        <?= e($barbeiro['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <input 
                                type="datetime-local" 
                                name="data_hora"
                                value="<?= e(formatDateTimeLocal($agendamento['data_hora'])) ?>"
                                required
                            >

                            <select name="status">
                                <option value="pendente" <?= selected('pendente', $agendamento['status']) ?>>Pendente</option>
                                <option value="agendado" <?= selected('agendado', $agendamento['status']) ?>>Agendado</option>
                                <option value="cancelado" <?= selected('cancelado', $agendamento['status']) ?>>Cancelado</option>
                            </select>

                            <select name="servicos[]" multiple class="full">
                                <?php foreach ($servicos as $servico): ?>
                                    <option 
                                        value="<?= (int) $servico['id_servico'] ?>"
                                        <?= checkedService($servico['id_servico'], $selectedServices) ?>
                                    >
                                        <?= e($servico['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <textarea name="descricao" class="full"><?= e($agendamento['descricao'] ?? '') ?></textarea>

                            <button type="submit" class="btn-main">Salvar alterações</button>
                        </form>

                        <form 
                            action="index.php?action=admin_scheduling_delete" 
                            method="POST"
                            onsubmit="return confirm('Deseja excluir este agendamento?');"
                        >
                            <input type="hidden" name="id_agendamento" value="<?= (int) $agendamento['id_agendamento'] ?>">
                            <button type="submit" class="btn-danger">Excluir agendamento</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </section>
</main>

</body>
</html>