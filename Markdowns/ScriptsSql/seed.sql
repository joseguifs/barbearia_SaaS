USE barbertime;


-- =========================================================
-- DADOS INICIAIS DO SISTEMA
-- Senhas de teste:
-- Admin: admin123
-- Cliente: cliente123
-- Barbeiros: barbeiro123
-- =========================================================

INSERT INTO admin (id_admin, nome, email, senha)
VALUES (
    1,
    'Administrador',
    'admin@barbertime.com',
    '$2y$12$2XlPzmqy4DY8ryuPCc812O7XbAQbQdvf9YlojqtigBcRBaSs7XtwG'
);

INSERT INTO cliente (nome, telefone, email, senha)
VALUES
(
    'Cliente Teste',
    '(63) 99999-0001',
    'cliente@teste.com',
    '$2y$12$0TJHPbZkNBRSrCkPj3mqO.2WV.aP2n2evD5eJJcdwzcC/W9ay4HHS'
);

INSERT INTO servico (nome, descricao, preco, duracao)
VALUES
(
    'Corte Masculino',
    'Corte masculino tradicional ou moderno, conforme preferência do cliente.',
    35.00,
    40
),
(
    'Barba',
    'Aparar e modelar a barba com acabamento profissional.',
    25.00,
    30
),
(
    'Corte + Barba',
    'Pacote completo com corte masculino e barba.',
    55.00,
    70
),
(
    'Sobrancelha',
    'Design e acabamento de sobrancelha.',
    15.00,
    15
);

INSERT INTO barbeiro (nome, email, senha)
VALUES
(
    'José',
    'josebarbeiro@gmail.com',
    '$2y$12$nGiWKk0ksqYFfrHXjenk3.KRbQvmi48pTIIjQPfb3NzNYj.d/qpu2'
),
(
    'Carlos',
    'carlosbarbeiro@gmail.com',
    '$2y$12$nGiWKk0ksqYFfrHXjenk3.KRbQvmi48pTIIjQPfb3NzNYj.d/qpu2'
);

-- Relaciona barbeiros aos serviços
-- IDs esperados:
-- Serviços:
-- 1 = Corte Masculino
-- 2 = Barba
-- 3 = Corte + Barba
-- 4 = Sobrancelha
--
-- Barbeiros:
-- 1 = José
-- 2 = Carlos

INSERT INTO barbeiro_servico (id_barbeiro, id_servico)
VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 3),
(2, 4);
