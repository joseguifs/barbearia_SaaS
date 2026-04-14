<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos Pendentes</title>
    <link rel="stylesheet" href="/app/css/admin.css">
</head>
<body>

<h1>Agendamentos Pendentes</h1>

<?php if (!empty($_GET['success'])): ?>
    <p class="success"><?= htmlspecialchars($_GET['success']) ?></p>
<?php endif; ?>

<?php if (empty($agendamentos)): ?>
    <p>Nenhum agendamento pendente.</p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Barbeiro</th>
            <th>Data/Hora</th>
            <th>Serviços</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($agendamentos as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['cliente']) ?></td>
            <td><?= htmlspecialchars($a['barbeiro']) ?></td>
            <td><?= htmlspecialchars($a['data_hora']) ?></td>
            <td><?= htmlspecialchars($a['servicos']) ?></td>

            <td>
                <!-- Aceitar -->
                <form method="POST" action="/index.php?action=review_accept">
                    <input type="hidden" name="id_agendamento" value="<?= $a['id_agendamento'] ?>">
                    <button type="submit">✔</button>
                </form>

                <!-- Recusar -->
                <form method="POST" action="/index.php?action=review_reject">
                    <input type="hidden" name="id_agendamento" value="<?= $a['id_agendamento'] ?>">
                    <button type="submit">✖</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<?php endif; ?>

</body>
</html>