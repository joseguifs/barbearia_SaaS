DROP SCHEMA IF EXISTS barbertime;
CREATE SCHEMA barbertime DEFAULT CHARACTER SET utf8mb4;
USE barbertime;

-- -----------------------------------------------------
-- Tabela cliente
-- -----------------------------------------------------
CREATE TABLE cliente (
  id_cliente INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  email VARCHAR(155) NULL,
  senha VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_cliente),
  UNIQUE KEY uq_cliente_email (email)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela barbeiro
-- -----------------------------------------------------
CREATE TABLE barbeiro (
  id_barbeiro INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_barbeiro)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela servico
-- -----------------------------------------------------
CREATE TABLE servico (
  id_servico INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  duracao INT NULL,
  PRIMARY KEY (id_servico)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Relação many-to-many entre barbeiro e serviço
-- Um barbeiro pode fazer vários serviços
-- Um serviço pode ser feito por vários barbeiros
-- -----------------------------------------------------
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

CREATE INDEX idx_barbeiro_servico_servico
ON barbeiro_servico (id_servico);

-- -----------------------------------------------------
-- Tabela agendamento
-- Não guarda mais id_servico
-- Agora o(s) serviço(s) ficam na tabela agendamento_servico
-- -----------------------------------------------------
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

CREATE INDEX idx_agendamento_cliente
ON agendamento (id_cliente);

CREATE INDEX idx_agendamento_barbeiro
ON agendamento (id_barbeiro);

CREATE INDEX idx_agendamento_data_hora
ON agendamento (data_hora);

-- impede 2 agendamentos para o mesmo barbeiro no mesmo horário
CREATE UNIQUE INDEX uq_agendamento_barbeiro_data_hora
ON agendamento (id_barbeiro, data_hora);

-- -----------------------------------------------------
-- Relação many-to-many entre agendamento e serviço
-- Um agendamento pode ter vários serviços
-- -----------------------------------------------------
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

CREATE INDEX idx_agendamento_servico_servico
ON agendamento_servico (id_servico);