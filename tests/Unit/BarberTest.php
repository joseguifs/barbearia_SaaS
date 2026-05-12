<?php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../test_database.php';
require_once __DIR__ . '/../../app/models/Barber.php';

$testPdo = createTestPdo();

function seedService(PDO $pdo, $nome = 'Barba', $preco = 25.00, $duracao = 40)
{
    $sql = "INSERT INTO servico (nome, preco, duracao)
            VALUES (:nome, :preco, :duracao)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':preco', $preco);
    $stmt->bindValue(':duracao', $duracao, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

function seedClient(PDO $pdo, $nome = 'Cliente Teste')
{
    $sql = "INSERT INTO cliente (nome, telefone, email, senha)
            VALUES (:nome, :telefone, :email, :senha)";

    $email = strtolower(str_replace(' ', '.', $nome)) . uniqid() . '@teste.com';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':telefone', '63999990000');
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', password_hash('123456', PASSWORD_DEFAULT));
    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

function seedScheduling(PDO $pdo, $idCliente, $idBarbeiro, $status = 'agendado')
{
    $sql = "INSERT INTO agendamento (
                id_cliente,
                id_barbeiro,
                data_hora,
                descricao,
                status
            ) VALUES (
                :id_cliente,
                :id_barbeiro,
                :data_hora,
                :descricao,
                :status
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
    $stmt->bindValue(':id_barbeiro', $idBarbeiro, PDO::PARAM_INT);
    $stmt->bindValue(':data_hora', '2026-12-20 10:00:00');
    $stmt->bindValue(':descricao', 'Agendamento de teste');
    $stmt->bindValue(':status', $status);
    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

runTest('Deve cadastrar barbeiro sem serviço', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('João Silva', 'joao@teste.com', []);

    assertNotEmptyValue($idBarbeiro, 'O ID do barbeiro cadastrado não deve estar vazio.');

    $barber = $barberModel->find($idBarbeiro);

    assertEqualsValue('João Silva', $barber['nome'], 'O nome do barbeiro cadastrado está incorreto.');
    assertEqualsValue('joao@teste.com', $barber['email'], 'O e-mail do barbeiro cadastrado está incorreto.');
});

runTest('Deve cadastrar barbeiro sem e-mail', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Barbeiro Sem Email', null, []);

    $barber = $barberModel->find($idBarbeiro);

    assertEqualsValue('Barbeiro Sem Email', $barber['nome'], 'O nome do barbeiro está incorreto.');
    assertTrueValue($barber['email'] === null, 'O e-mail deveria ser nulo.');
});

runTest('Deve impedir cadastro com e-mail inválido', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    try {
        $barberModel->create('Barbeiro Email Inválido', 'email-invalido', []);
        throw new Exception('O cadastro com e-mail inválido deveria falhar.');

    } catch (InvalidArgumentException $e) {
        assertEqualsValue(
            'O e-mail do barbeiro é inválido.',
            $e->getMessage(),
            'A mensagem de erro do e-mail inválido está incorreta.'
        );
    }
});

runTest('Deve cadastrar barbeiro com serviço relacionado', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $idServico = seedService($testPdo, 'Barba', 25.00, 40);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Carlos Santos', 'carlos@teste.com', [$idServico]);

    $barber = $barberModel->findWithServices($idBarbeiro);

    assertEqualsValue('Carlos Santos', $barber['nome'], 'O nome do barbeiro está incorreto.');
    assertEqualsValue('carlos@teste.com', $barber['email'], 'O e-mail do barbeiro está incorreto.');
    assertTrueValue(count($barber['servicos']) === 1, 'O barbeiro deveria ter 1 serviço relacionado.');
    assertEqualsValue('Barba', $barber['servicos'][0]['nome'], 'O serviço relacionado está incorreto.');
});

runTest('Deve impedir cadastro com serviço inexistente', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    try {
        $barberModel->create('Barbeiro Serviço Inválido', 'servico.invalido@teste.com', [999]);
        throw new Exception('O cadastro com serviço inexistente deveria falhar.');

    } catch (InvalidArgumentException $e) {
        assertTrueValue(
            strpos($e->getMessage(), 'Os seguintes serviços não existem no banco') !== false,
            'A mensagem de erro para serviço inexistente está incorreta.'
        );
    }
});

runTest('Deve listar todos os barbeiros com serviços', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $idServico = seedService($testPdo, 'Cabelo', 35.00, 50);

    $barberModel = new Barber($testPdo);

    $barberModel->create('Carlos Santos', 'carlos@teste.com', [$idServico]);
    $barberModel->create('João Silva', 'joao@teste.com', []);

    $barbers = $barberModel->all();

    assertTrueValue(count($barbers) === 2, 'A listagem deveria retornar 2 barbeiros.');
    assertTrueValue(isset($barbers[0]['servicos']), 'A listagem deveria retornar o campo servicos.');
    assertTrueValue(isset($barbers[0]['especialidades']), 'A listagem deveria retornar o campo especialidades.');
});

runTest('Deve buscar barbeiro por ID', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Mario Felipe', 'mario@teste.com', []);

    $barber = $barberModel->find($idBarbeiro);

    assertEqualsValue('Mario Felipe', $barber['nome'], 'O barbeiro buscado por ID está incorreto.');
    assertEqualsValue('mario@teste.com', $barber['email'], 'O e-mail do barbeiro buscado por ID está incorreto.');
});

runTest('Deve retornar falso ao buscar barbeiro inexistente', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $barber = $barberModel->find(9999);

    assertTrueValue($barber === false, 'A busca por barbeiro inexistente deveria retornar false.');
});

runTest('Deve atualizar nome e e-mail do barbeiro', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Nome Antigo', 'antigo@teste.com', []);

    $updated = $barberModel->update($idBarbeiro, 'Nome Novo', 'novo@teste.com', []);

    assertTrueValue($updated === true, 'O método update deveria retornar true.');

    $barber = $barberModel->find($idBarbeiro);

    assertEqualsValue('Nome Novo', $barber['nome'], 'O nome do barbeiro não foi atualizado corretamente.');
    assertEqualsValue('novo@teste.com', $barber['email'], 'O e-mail do barbeiro não foi atualizado corretamente.');
});

runTest('Deve atualizar serviços do barbeiro', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $idServico1 = seedService($testPdo, 'Barba', 25.00, 40);
    $idServico2 = seedService($testPdo, 'Cabelo', 35.00, 50);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Carlos Santos', 'carlos@teste.com', [$idServico1]);

    $barberModel->update($idBarbeiro, 'Carlos Santos', 'carlos@teste.com', [$idServico2]);

    $barber = $barberModel->findWithServices($idBarbeiro);

    assertTrueValue(count($barber['servicos']) === 1, 'O barbeiro deveria ter apenas 1 serviço após atualização.');
    assertEqualsValue('Cabelo', $barber['servicos'][0]['nome'], 'O serviço atualizado está incorreto.');
});

runTest('Deve remover todos os serviços do barbeiro', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $idServico1 = seedService($testPdo, 'Barba', 25.00, 40);
    $idServico2 = seedService($testPdo, 'Cabelo', 35.00, 50);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Carlos Santos', 'carlos@teste.com', [$idServico1, $idServico2]);

    $barberModel->syncServices($idBarbeiro, []);

    $barber = $barberModel->findWithServices($idBarbeiro);

    assertTrueValue(count($barber['servicos']) === 0, 'O barbeiro deveria ficar sem serviços relacionados.');
});

runTest('Deve excluir barbeiro sem agendamentos', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idBarbeiro = $barberModel->create('Barbeiro Teste', 'teste@teste.com', []);

    $result = $barberModel->delete($idBarbeiro);

    assertTrueValue(is_array($result), 'O retorno da exclusão deveria ser um array.');
    assertTrueValue($result['success'] === true, 'A exclusão deveria retornar success true.');

    $barber = $barberModel->find($idBarbeiro);

    assertTrueValue($barber === false, 'O barbeiro deveria ter sido excluído.');
});

runTest('Deve redirecionar agendamentos para próximo barbeiro ao excluir', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idCliente = seedClient($testPdo);

    $idBarbeiro1 = $barberModel->create('Barbeiro Um', 'um@teste.com', []);
    $idBarbeiro2 = $barberModel->create('Barbeiro Dois', 'dois@teste.com', []);

    $idAgendamento = seedScheduling($testPdo, $idCliente, $idBarbeiro1, 'agendado');

    $result = $barberModel->delete($idBarbeiro1);

    assertTrueValue($result['success'] === true, 'A exclusão deveria retornar success true.');
    assertEqualsValue(1, $result['reassigned_scheduling_count'], 'Deveria redirecionar 1 agendamento.');
    assertEqualsValue($idBarbeiro2, $result['new_barber']['id_barbeiro'], 'O agendamento deveria ir para o próximo barbeiro.');

    $sql = "SELECT id_barbeiro, status
            FROM agendamento
            WHERE id_agendamento = :id_agendamento";

    $stmt = $testPdo->prepare($sql);
    $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
    $stmt->execute();

    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    assertEqualsValue($idBarbeiro2, $agendamento['id_barbeiro'], 'O id_barbeiro do agendamento não foi atualizado.');
    assertEqualsValue('pendente', $agendamento['status'], 'O status do agendamento deveria voltar para pendente.');
});

runTest('Deve impedir exclusão se barbeiro possui agendamento e não existe outro barbeiro', function () use ($testPdo) {
    resetTestDatabase($testPdo);

    $barberModel = new Barber($testPdo);

    $idCliente = seedClient($testPdo);

    $idBarbeiro = $barberModel->create('Barbeiro Único', 'unico@teste.com', []);

    seedScheduling($testPdo, $idCliente, $idBarbeiro, 'agendado');

    $result = $barberModel->delete($idBarbeiro);

    assertTrueValue($result['success'] === false, 'A exclusão deveria falhar.');
    assertEqualsValue('no_replacement_barber', $result['reason'], 'O motivo da falha está incorreto.');

    $barber = $barberModel->find($idBarbeiro);

    assertTrueValue($barber !== false, 'O barbeiro não deveria ser excluído.');
});