<?php

$host = "localhost";
$user = "root";
$pass = "jose2005";
$db = "";

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("DROP DATABASE IF EXISTS {$db}");
    $pdo->exec("CREATE DATABASE {$db} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$db}");

    $pdo->exec("
        CREATE TABLE cliente (
            id_cliente INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            telefone VARCHAR(20) NOT NULL,
            email VARCHAR(155) NULL,
            senha VARCHAR(255) NOT NULL,
            PRIMARY KEY (id_cliente),
            UNIQUE KEY uq_cliente_email (email)
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE TABLE barbeiro (
            id_barbeiro INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(155) NULL,
            PRIMARY KEY (id_barbeiro),
            UNIQUE KEY uq_barbeiro_email (email)
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE TABLE servico (
            id_servico INT NOT NULL AUTO_INCREMENT,
            nome VARCHAR(100) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            duracao INT NULL,
            PRIMARY KEY (id_servico)
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE TABLE barbeiro_servico (
            id_barbeiro INT NOT NULL,
            id_servico INT NOT NULL,
            PRIMARY KEY (id_barbeiro, id_servico),

            CONSTRAINT fk_barbeiro_servico_barbeiro
                FOREIGN KEY (id_barbeiro)
                REFERENCES barbeiro (id_barbeiro)
                ON DELETE CASCADE
                ON UPDATE CASCADE,

            CONSTRAINT fk_barbeiro_servico_servico
                FOREIGN KEY (id_servico)
                REFERENCES servico (id_servico)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE INDEX idx_barbeiro_servico_servico
        ON barbeiro_servico (id_servico);
    ");

    $pdo->exec("
        CREATE TABLE agendamento (
            id_agendamento INT NOT NULL AUTO_INCREMENT,
            id_cliente INT NOT NULL,
            id_barbeiro INT NOT NULL,
            data_hora DATETIME NOT NULL,
            descricao TEXT NULL,
            status ENUM('pendente', 'agendado', 'cancelado', 'concluido', 'faltou') NOT NULL DEFAULT 'pendente',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

            PRIMARY KEY (id_agendamento),

            CONSTRAINT fk_agendamento_cliente
                FOREIGN KEY (id_cliente)
                REFERENCES cliente (id_cliente)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,

            CONSTRAINT fk_agendamento_barbeiro
                FOREIGN KEY (id_barbeiro)
                REFERENCES barbeiro (id_barbeiro)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE INDEX idx_agendamento_cliente
        ON agendamento (id_cliente);
    ");

    $pdo->exec("
        CREATE INDEX idx_agendamento_barbeiro
        ON agendamento (id_barbeiro);
    ");

    $pdo->exec("
        CREATE INDEX idx_agendamento_data_hora
        ON agendamento (data_hora);
    ");

    $pdo->exec("
        CREATE UNIQUE INDEX uq_agendamento_barbeiro_data_hora
        ON agendamento (id_barbeiro, data_hora);
    ");

    $pdo->exec("
        CREATE TABLE agendamento_servico (
            id_agendamento INT NOT NULL,
            id_servico INT NOT NULL,
            PRIMARY KEY (id_agendamento, id_servico),

            CONSTRAINT fk_agendamento_servico_agendamento
                FOREIGN KEY (id_agendamento)
                REFERENCES agendamento (id_agendamento)
                ON DELETE CASCADE
                ON UPDATE CASCADE,

            CONSTRAINT fk_agendamento_servico_servico
                FOREIGN KEY (id_servico)
                REFERENCES servico (id_servico)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ) ENGINE=InnoDB;
    ");

    $pdo->exec("
        CREATE INDEX idx_agendamento_servico_servico
        ON agendamento_servico (id_servico);
    ");

    echo "Banco de testes {$db} criado com sucesso." . PHP_EOL;

} catch (PDOException $e) {
    echo "Erro ao criar banco de testes: " . $e->getMessage() . PHP_EOL;
    exit(1);
}