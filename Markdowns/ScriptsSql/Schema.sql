CREATE DATABASE IF NOT EXISTS barbertime
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE barbertime;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS agendamento_servico;
DROP TABLE IF EXISTS barbeiro_servico;
DROP TABLE IF EXISTS agendamento;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS servico;
DROP TABLE IF EXISTS barbeiro;
DROP TABLE IF EXISTS cliente;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admin (
    id_admin TINYINT(3) UNSIGNED NOT NULL DEFAULT 1,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin),
    UNIQUE KEY email (email),
    CONSTRAINT chk_single_admin CHECK (id_admin = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cliente (
    id_cliente INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(155) DEFAULT NULL,
    senha VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_cliente),
    UNIQUE KEY uq_cliente_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE barbeiro (
    id_barbeiro INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_barbeiro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE servico (
    id_servico INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT DEFAULT NULL,
    preco DECIMAL(10,2) NOT NULL,
    duracao INT(11) DEFAULT NULL,
    PRIMARY KEY (id_servico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE agendamento (
    id_agendamento INT(11) NOT NULL AUTO_INCREMENT,
    id_cliente INT(11) NOT NULL,
    id_barbeiro INT(11) NOT NULL,
    data_hora DATETIME NOT NULL,
    descricao TEXT DEFAULT NULL,
    status ENUM('pendente','agendado','cancelado','concluido','faltou') NOT NULL DEFAULT 'pendente',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_agendamento),
    UNIQUE KEY uq_agendamento_barbeiro_data_hora (id_barbeiro, data_hora),
    KEY idx_agendamento_cliente (id_cliente),
    KEY idx_agendamento_barbeiro (id_barbeiro),
    KEY idx_agendamento_data_hora (data_hora),
    CONSTRAINT fk_agendamento_barbeiro
        FOREIGN KEY (id_barbeiro)
        REFERENCES barbeiro (id_barbeiro)
        ON UPDATE CASCADE,
    CONSTRAINT fk_agendamento_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES cliente (id_cliente)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE barbeiro_servico (
    id_barbeiro INT(11) NOT NULL,
    id_servico INT(11) NOT NULL,
    PRIMARY KEY (id_barbeiro, id_servico),
    KEY idx_barbeiro_servico_servico (id_servico),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE agendamento_servico (
    id_agendamento INT(11) NOT NULL,
    id_servico INT(11) NOT NULL,
    PRIMARY KEY (id_agendamento, id_servico),
    KEY idx_agendamento_servico_servico (id_servico),
    CONSTRAINT fk_agendamento_servico_agendamento
        FOREIGN KEY (id_agendamento)
        REFERENCES agendamento (id_agendamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_agendamento_servico_servico
        FOREIGN KEY (id_servico)
        REFERENCES servico (id_servico)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
