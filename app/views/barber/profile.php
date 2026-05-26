<?php
if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>BarberTime - Perfil do Barbeiro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #050505;
            --panel: #0b0b0d;
            --gold: #b98c54;
            --gold-soft: #d5a86d;
            --border: #b98c54;
            --text: #c19762;
            --white-soft: #e9e9e9;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url('/BARBEARIA_SAAS/public/assets/images/backgroundbarbearia.jpg') center center / cover fixed;
            font-family: 'Oswald', sans-serif;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .profile-container {
            width: 100%;
            max-width: 800px;
            background: rgba(10, 10, 12, 0.95);
            border: 2px solid rgba(185, 140, 84, 0.45);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .header {
            background: #080808;
            padding: 25px 30px;
            border-bottom: 2px solid var(--border);
            text-align: center;
        }

        .header h1 {
            color: var(--gold);
            font-size: 2.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header p {
            color: var(--text);
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .agenda-content {
            padding: 30px;
        }

        .agenda-title {
            color: var(--white-soft);
            font-size: 1.4rem;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(185, 140, 84, 0.2);
            padding-bottom: 10px;
        }

        .agenda-title span {
            color: var(--gold);
        }

        .card-agendamento {
            background: rgba(255, 255, 255, 0.03);
            border-left: 4px solid var(--gold);
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .card-agendamento:hover {
            background: rgba(185, 140, 84, 0.1);
        }

        .horario {
            font-size: 1.8rem;
            color: var(--gold-soft);
            font-weight: 600;
            min-width: 90px;
        }

        .info-cliente {
            flex-grow: 1;
            margin-left: 20px;
        }

        .info-cliente h3 {
            color: var(--white-soft);
            font-size: 1.3rem;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .info-cliente p {
            color: var(--text);
            font-size: 0.95rem;
            font-family: Arial, sans-serif;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-pendente { background: rgba(185, 140, 84, 0.2); color: var(--gold); border: 1px solid var(--gold); }
        .status-agendado { background: rgba(46, 125, 50, 0.2); color: #81c784; border: 1px solid #81c784; }
        .status-concluido { background: rgba(255, 255, 255, 0.1); color: #fff; border: 1px solid #fff; }
        .status-cancelado { background: rgba(211, 47, 47, 0.2); color: #e57373; border: 1px solid #e57373; }

        .empty-message {
            text-align: center;
            color: var(--text);
            padding: 40px 0;
            font-size: 1.2rem;
        }

        @media (max-width: 600px) {
            .card-agendamento {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-cliente {
                margin-left: 0;
                margin-top: 10px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="header">
        <h1>BEM-VINDO, <?= e($dadosBarbeiro['nome']) ?></h1>
        <p>Painel de Controle do Barbeiro</p>
    </div>

    <div class="agenda-content">
        <div class="agenda-title">
            Agenda do Dia
            <span><?= date('d/m/Y') ?></span>
        </div>

        <?php if (empty($agendamentos)): ?>
            <div class="empty-message">
                Nenhum agendamento programado para hoje.
            </div>
        <?php else: ?>
            <?php foreach ($agendamentos as $agendamento): ?>
                <div class="card-agendamento">
                    <div class="horario">
                        <?= date('H:i', strtotime($agendamento['data_hora'])) ?>
                    </div>
                    
                    <div class="info-cliente">
                        <h3><?= e($agendamento['cliente_nome']) ?></h3>
                        <p><strong>Serviço(s):</strong> <?= e($agendamento['servicos_texto']) ?></p>
                        <?php if(!empty($agendamento['descricao'])): ?>
                            <p><strong>Obs:</strong> <?= e($agendamento['descricao']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="status-badge status-<?= e($agendamento['status']) ?>">
                        <?= e($agendamento['status']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>