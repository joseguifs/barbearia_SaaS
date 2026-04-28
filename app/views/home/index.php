<?php
function e($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Temporária</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #111;
            color: #f1d3a3;
            font-family: Arial, sans-serif;
        }

        .box {
            text-align: center;
            padding: 32px;
            border: 1px solid #b98c54;
            border-radius: 10px;
            background: #1a1a1a;
            max-width: 500px;
        }

        h1 {
            margin-bottom: 14px;
        }

        p {
            margin-bottom: 18px;
        }

        a {
            display: inline-block;
            padding: 10px 18px;
            background: #b98c54;
            color: #111;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Login realizado com sucesso</h1>
        <p>Bem-vindo, <?= e($clienteNome) ?>.</p>
        <p>Esta é uma home temporária até a outra feature ficar pronta.</p>
        <a href="index.php?action=logout">Sair</a>
    </div>
</body>
</html>