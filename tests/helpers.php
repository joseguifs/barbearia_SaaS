<?php

function assertTrueValue($condition, $message)
{
    if (!$condition) {
        throw new Exception($message);
    }
}

function assertEqualsValue($expected, $actual, $message)
{
    if ($expected != $actual) {
        throw new Exception($message . " | Esperado: {$expected}, recebido: {$actual}");
    }
}

function assertNotEmptyValue($value, $message)
{
    if (empty($value)) {
        throw new Exception($message);
    }
}

function runTest($name, $callback)
{
    try {
        $callback();
        echo "[PASS] {$name}" . PHP_EOL;

    } catch (Exception $e) {
        echo "[FAIL] {$name}" . PHP_EOL;
        echo "       " . $e->getMessage() . PHP_EOL;
    }
}

function resetTestDatabase(PDO $pdo)
{
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = [
        'agendamento_servico',
        'barbeiro_servico',
        'agendamento',
        'admin',
        'cliente',
        'barbeiro',
        'servico'
    ];

    foreach ($tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE {$table}");
        } catch (Exception $e) {
            // Ignora tabelas que não existirem
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}