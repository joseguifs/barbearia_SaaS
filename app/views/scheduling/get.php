<?php
function e($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function formatarDataHoraExtenso($dataHora)
{
    $diasSemana = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];

    $meses = [
        '01' => 'janeiro',
        '02' => 'fevereiro',
        '03' => 'março',
        '04' => 'abril',
        '05' => 'maio',
        '06' => 'junho',
        '07' => 'julho',
        '08' => 'agosto',
        '09' => 'setembro',
        '10' => 'outubro',
        '11' => 'novembro',
        '12' => 'dezembro'
    ];

    $timestamp = strtotime($dataHora);

    $diaSemana = $diasSemana[date('l', $timestamp)] ?? date('l', $timestamp);
    $dia = date('d', $timestamp);
    $mes = $meses[date('m', $timestamp)] ?? date('m', $timestamp);
    $hora = date('H:i', $timestamp);

    return "{$diaSemana}, {$dia} de {$mes} às {$hora}";
}

function formatarStatus($status)
{
    $statusFormatado = [
        'pendente' => 'Pendente',
        'agendado' => 'Agendado',
        'cancelado' => 'Cancelado',
        'concluido' => 'Concluído',
        'faltou' => 'Faltou'
    ];

    return $statusFormatado[$status] ?? ucfirst($status);
}

$nomesServicos = [];

foreach ($servicos as $servico) {
    $nomesServicos[] = e($servico['nome']);
}

$servicoTexto = !empty($nomesServicos)
    ? implode(' + ', $nomesServicos)
    : 'Nenhum serviço informado';

$status = $agendamento['status'] ?? 'pendente';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Agendamento</title>

    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/scheduling.css">
    <link rel="stylesheet" href="/BARBEARIA_SAAS/app/css/scheduling-get.css">
</head>
<body>
    <main class="details-page">
        <section class="details-card">

            <div class="detail-item">
                <span class="detail-label">Status</span>
                <p class="detail-value">
                    <span class="status-badge status-<?= e($status) ?>">
                        <?= e(formatarStatus($status)) ?>
                    </span>
                </p>
            </div>

            <div class="detail-item">
                <span class="detail-label">Serviço</span>
                <p class="detail-value"><?= $servicoTexto ?></p>
            </div>

            <div class="detail-item">
                <span class="detail-label">Barbeiro</span>
                <p class="detail-value"><?= e($agendamento['barbeiro_nome']) ?></p>
            </div>

            <div class="detail-item">
                <span class="detail-label">Data/Horário</span>
                <p class="detail-value"><?= formatarDataHoraExtenso($agendamento['data_hora']) ?></p>
            </div>

            <div class="detail-item">
                <span class="detail-label">Nome do Cliente</span>
                <p class="detail-value"><?= e($agendamento['cliente_nome']) ?></p>
            </div>

            <div class="detail-item">
                <span class="detail-label">Valor</span>
                <p class="detail-value">R$ <?= number_format($valorTotal, 2, ',', '.') ?></p>
            </div>

            <?php if (!empty($agendamento['descricao'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Observações</span>
                    <p class="detail-value"><?= nl2br(e($agendamento['descricao'])) ?></p>
                </div>
            <?php endif; ?>

        </section>
    </main>
</body>
</html>