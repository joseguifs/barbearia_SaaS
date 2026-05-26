
<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/models/Barber.php';

class BarberTest extends TestCase
{
    public function testCreateBarberWithEmail(): void
    {
        $barberModel = new Barber($this->pdo);

        $id = $barberModel->create('Carlos Santos', 'carlos@teste.com', []);

        $this->assertNotEmpty($id);

        $barber = $barberModel->find($id);

        $this->assertEquals('Carlos Santos', $barber['nome']);
        $this->assertEquals('carlos@teste.com', $barber['email']);
    }

    public function testCreateBarberWithoutEmail(): void
    {
        $barberModel = new Barber($this->pdo);

        $id = $barberModel->create('Barbeiro Sem Email', null, []);

        $barber = $barberModel->find($id);

        $this->assertEquals('Barbeiro Sem Email', $barber['nome']);
        $this->assertNull($barber['email']);
    }

    public function testCreateBarberWithInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O e-mail do barbeiro é inválido.');

        $barberModel = new Barber($this->pdo);

        $barberModel->create('Barbeiro Inválido', 'email-invalido', []);
    }

    public function testCreateBarberWithService(): void
    {
        $idServico = $this->createService('Barba', 25.00, 40);

        $barberModel = new Barber($this->pdo);

        $idBarbeiro = $barberModel->create(
            'Carlos Santos',
            'carlos.servico@teste.com',
            [$idServico]
        );

        $barber = $barberModel->findWithServices($idBarbeiro);

        $this->assertEquals('Carlos Santos', $barber['nome']);
        $this->assertEquals('carlos.servico@teste.com', $barber['email']);
        $this->assertCount(1, $barber['servicos']);
        $this->assertEquals('Barba', $barber['servicos'][0]['nome']);
    }

    public function testCreateBarberWithInvalidServiceThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $barberModel = new Barber($this->pdo);

        $barberModel->create(
            'Barbeiro Serviço Inválido',
            'servico.invalido@teste.com',
            [999]
        );
    }

    public function testListBarbersWithServices(): void
    {
        $idServico = $this->createService('Cabelo', 35.00, 50);

        $barberModel = new Barber($this->pdo);

        $barberModel->create('Carlos Santos', 'carlos@teste.com', [$idServico]);
        $barberModel->create('João Silva', 'joao@teste.com', []);

        $barbers = $barberModel->all();

        $this->assertCount(2, $barbers);
        $this->assertArrayHasKey('servicos', $barbers[0]);
        $this->assertArrayHasKey('especialidades', $barbers[0]);
    }

    public function testFindBarberById(): void
    {
        $barberModel = new Barber($this->pdo);

        $id = $barberModel->create('Mario Felipe', 'mario@teste.com', []);

        $barber = $barberModel->find($id);

        $this->assertEquals('Mario Felipe', $barber['nome']);
        $this->assertEquals('mario@teste.com', $barber['email']);
    }

    public function testFindNonexistentBarberReturnsFalse(): void
    {
        $barberModel = new Barber($this->pdo);

        $barber = $barberModel->find(9999);

        $this->assertFalse($barber);
    }

    public function testUpdateBarberNameAndEmail(): void
    {
        $barberModel = new Barber($this->pdo);

        $id = $barberModel->create('Nome Antigo', 'antigo@teste.com', []);

        $updated = $barberModel->update($id, 'Nome Novo', 'novo@teste.com', []);

        $this->assertTrue($updated);

        $barber = $barberModel->find($id);

        $this->assertEquals('Nome Novo', $barber['nome']);
        $this->assertEquals('novo@teste.com', $barber['email']);
    }

    public function testUpdateBarberServices(): void
    {
        $idServico1 = $this->createService('Barba', 25.00, 40);
        $idServico2 = $this->createService('Cabelo', 35.00, 50);

        $barberModel = new Barber($this->pdo);

        $idBarbeiro = $barberModel->create(
            'Carlos Santos',
            'carlos@teste.com',
            [$idServico1]
        );

        $barberModel->update(
            $idBarbeiro,
            'Carlos Santos',
            'carlos@teste.com',
            [$idServico2]
        );

        $barber = $barberModel->findWithServices($idBarbeiro);

        $this->assertCount(1, $barber['servicos']);
        $this->assertEquals('Cabelo', $barber['servicos'][0]['nome']);
    }

    public function testRemoveAllBarberServices(): void
    {
        $idServico1 = $this->createService('Barba', 25.00, 40);
        $idServico2 = $this->createService('Cabelo', 35.00, 50);

        $barberModel = new Barber($this->pdo);

        $idBarbeiro = $barberModel->create(
            'Carlos Santos',
            'carlos@teste.com',
            [$idServico1, $idServico2]
        );

        $barberModel->syncServices($idBarbeiro, []);

        $barber = $barberModel->findWithServices($idBarbeiro);

        $this->assertCount(0, $barber['servicos']);
    }

    public function testDeleteBarberWithoutSchedulings(): void
    {
        $barberModel = new Barber($this->pdo);

        $id = $barberModel->create('Barbeiro Teste', 'teste@teste.com', []);

        $result = $barberModel->delete($id);

        $this->assertTrue($result['success']);

        $barber = $barberModel->find($id);

        $this->assertFalse($barber);
    }

    public function testDeleteBarberWithSchedulingRedirectsToNextBarber(): void
    {
        $barberModel = new Barber($this->pdo);

        $idCliente = $this->createClient();

        $idBarbeiro1 = $barberModel->create('Barbeiro Um', 'um@teste.com', []);
        $idBarbeiro2 = $barberModel->create('Barbeiro Dois', 'dois@teste.com', []);

        $idAgendamento = $this->createScheduling($idCliente, $idBarbeiro1, 'agendado');

        $result = $barberModel->delete($idBarbeiro1);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['reassigned_scheduling_count']);
        $this->assertEquals($idBarbeiro2, $result['new_barber']['id_barbeiro']);

        $stmt = $this->pdo->prepare("
            SELECT id_barbeiro, status
            FROM agendamento
            WHERE id_agendamento = :id_agendamento
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($idBarbeiro2, $agendamento['id_barbeiro']);
        $this->assertEquals('pendente', $agendamento['status']);
    }

    public function testDeleteBarberWithSchedulingFailsWhenNoOtherBarberExists(): void
    {
        $barberModel = new Barber($this->pdo);

        $idCliente = $this->createClient();

        $idBarbeiro = $barberModel->create('Barbeiro Único', 'unico@teste.com', []);

        $this->createScheduling($idCliente, $idBarbeiro, 'agendado');

        $result = $barberModel->delete($idBarbeiro);

        $this->assertFalse($result['success']);
        $this->assertEquals('no_replacement_barber', $result['reason']);

        $barber = $barberModel->find($idBarbeiro);

        $this->assertNotFalse($barber);
    }
}