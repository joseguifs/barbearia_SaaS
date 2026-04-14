dentro de admin/acceptOrRefuse clc esse cod: 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos Pendentes</title>
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling-review.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">BARBERTIME</div>
        <div class="user-area">
            <span class="user-name">GESTÃO DE AGENDAMENTOS</span>
            <div class="avatar"></div>
        </div>
    </header>

    <main class="page">
        <section class="card">
            <h2>Agendamentos Pendentes</h2>

            <?php if ($message === 'accepted'): ?>
                <div class="feedback success">Agendamento aceito com sucesso.</div>
            <?php endif; ?>

            <?php if ($message === 'rejected'): ?>
                <div class="feedback danger">Agendamento recusado com sucesso.</div>
            <?php endif; ?>

            <?php if (!empty($agendamentos)): ?>
                <div class="appointments-list">
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <h3><?= htmlspecialchars($agendamento['cliente_nome']) ?></h3>
                                <p><strong>Serviços:</strong> <?= htmlspecialchars($agendamento['servicos']) ?></p>
                                <p><strong>Barbeiro:</strong> <?= htmlspecialchars($agendamento['barbeiro_nome']) ?></p>
                                <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i', strtotime($agendamento['data_hora'])) ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($agendamento['status']) ?></p>
                            </div>

                            <div class="appointment-actions">
                                <form action="index.php?action=review_accept" method="POST">
                                    <input type="hidden" name="id_agendamento" value="<?= $agendamento['id_agendamento'] ?>">
                                    <button type="submit" class="btn-action btn-accept" title="Aceitar">✔</button>
                                </form>

                                <form action="index.php?action=review_reject" method="POST">
                                    <input type="hidden" name="id_agendamento" value="<?= $agendamento['id_agendamento'] ?>">
                                    <button type="submit" class="btn-action btn-reject" title="Recusar">✖</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-message">Nenhum agendamento pendente encontrado.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>