<?php
if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$agendamento = $agendamento ?? [];
$horariosDisponiveis = $horariosDisponiveis ?? ['09:00', '10:00', '11:30', '14:00', '15:30', '17:00', '18:30', '20:00'];
$dataSelecionada = $dataSelecionada ?? date('Y-m-d');
$horaSelecionada = $horaSelecionada ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Agendamento - BarberTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/scheduling-update.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">BARBERTIME</div>

        <nav class="nav">
            <a href="#">Serviços</a>
            <a href="#">Histórico</a>
            <a href="#">Perfil</a>
            <a href="#">Barbeiros</a>
        </nav>
    </header>

    <main class="update-page">
        <section class="update-header">
            <h1>Alterar Agendamento</h1>
            <p>Ajuste os detalhes do seu serviço exclusivo.</p>
        </section>

        <section class="update-layout">
            <aside class="left-column">
                <div class="info-card current-card">
                    <h2>Agendamento Atual</h2>

                    <div class="barber-box">
                        <img src="/BARBEARIA_SAAS/public/assets/images/barbearia.jpg" alt="Barbeiro">
                        <div>
                            <strong><?= e($agendamento['barbeiro_nome'] ?? 'Marcos') ?></strong>
                            <span>Master Barber</span>
                        </div>
                    </div>

                    <div class="current-details">
                        <p><span class="inline-icon">✂</span> <?= e($agendamento['servicos_texto'] ?? 'Corte executivo') ?></p>
                        <p><span class="inline-icon">📅</span> <?= e($agendamento['data_formatada'] ?? '25/10/2024') ?></p>
                        <p><span class="inline-icon">🕒</span> <?= e($agendamento['hora_formatada'] ?? '14:00') ?></p>
                    </div>
                </div>

                <div class="info-card quote-card">
                    <p>“A precisão é o nosso padrão. Escolha o novo horário que melhor se adapta à sua rotina de cavalheiro.”</p>
                </div>
            </aside>

            <section class="right-column">
                <form action="index.php?action=scheduling_update" method="POST">
                    <input type="hidden" name="id_agendamento" value="<?= e($agendamento['id_agendamento'] ?? '') ?>">

                    <div class="panel-card edit-panel">
                        <h2 class="panel-title">
                            <span class="title-icon">📅</span>
                            <span>Selecione a Data</span>
                        </h2>

                        <div class="calendar-box">
                            <label for="data_agendamento" class="field-label">Data</label>
                            <input
                                type="date"
                                id="data_agendamento"
                                name="data_agendamento"
                                class="date-input"
                                value="<?= e($dataSelecionada) ?>"
                            >
                        </div>
                    </div>

                    <div class="panel-card edit-panel schedule-times-panel">
                        <h2 class="panel-title">
                            <span class="title-icon">🕒</span>
                            <span>Horários Disponíveis</span>
                        </h2>

                        <div class="times-grid">
                            <?php foreach ($horariosDisponiveis as $horario): ?>
                                <label class="time-option">
                                    <input
                                        type="radio"
                                        name="hora_agendamento"
                                        value="<?= e($horario) ?>"
                                        <?= $horaSelecionada === $horario ? 'checked' : '' ?>
                                    >
                                    <span><?= e($horario) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="confirm-btn">Confirmar Alteração</button>
                        <a href="index.php?action=home" class="cancel-btn">Cancelar</a>
                    </div>
                </form>
            </section>
        </section>
    </main>
</body>
</html>