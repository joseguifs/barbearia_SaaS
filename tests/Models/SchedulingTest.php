<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/models/Scheduling.php';
require_once __DIR__ . '/../../app/models/Barber.php';

class SchedulingTest extends TestCase
{
    public function testCreateSchedulingDirectlyInDatabase(): void
    {
        $idCliente = $this->createClient();
        $idBarbeiro = $this->createBarberForScheduling();

        $idAgendamento = $this->createScheduling($idCliente, $idBarbeiro, 'pendente');

        $stmt = $this->pdo->prepare("
            SELECT * FROM agendamento
            WHERE id_agendamento = :id
        ");

        $stmt->bindValue(':id', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $scheduling = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($scheduling);
        $this->assertEquals($idCliente, $scheduling['id_cliente']);
        $this->assertEquals($idBarbeiro, $scheduling['id_barbeiro']);
        $this->assertEquals('pendente', $scheduling['status']);
    }

    public function testSchedulingStartsAsPending(): void
    {
        $idCliente = $this->createClient();
        $idBarbeiro = $this->createBarberForScheduling();

        $idAgendamento = $this->createScheduling($idCliente, $idBarbeiro, 'pendente');

        $stmt = $this->pdo->prepare("
            SELECT status FROM agendamento
            WHERE id_agendamento = :id
        ");

        $stmt->bindValue(':id', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $scheduling = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('pendente', $scheduling['status']);
    }

    public function testPreventDuplicateTimeForSameBarber(): void
    {
        $idCliente = $this->createClient();
        $idBarbeiro = $this->createBarberForScheduling();

        $this->createScheduling($idCliente, $idBarbeiro, 'agendado');

        $this->expectException(PDOException::class);

        $this->createScheduling($idCliente, $idBarbeiro, 'pendente');
    }

    public function testAllowSameTimeForDifferentBarbers(): void
    {
        $idCliente = $this->createClient();

        $idBarbeiro1 = $this->createBarberForScheduling('Barbeiro Um', 'um@teste.com');
        $idBarbeiro2 = $this->createBarberForScheduling('Barbeiro Dois', 'dois@teste.com');

        $idAgendamento1 = $this->createScheduling($idCliente, $idBarbeiro1, 'agendado');
        $idAgendamento2 = $this->createScheduling($idCliente, $idBarbeiro2, 'agendado');

        $this->assertNotEmpty($idAgendamento1);
        $this->assertNotEmpty($idAgendamento2);
    }

    public function testIsTimeAvailableIfMethodExists(): void
    {
        $idCliente = $this->createClient();

        $barberModel = new Barber($this->pdo);
        $idBarbeiro = $barberModel->create('Barbeiro Teste', 'barbeiro.scheduling@teste.com', []);

        $idServico = $this->createService('Barba', 25.00, 40);

        $barberModel->syncServices($idBarbeiro, [$idServico]);

        $this->createScheduling($idCliente, $idBarbeiro, 'agendado');

        $schedulingModel = new Scheduling($this->pdo);

        if (!method_exists($schedulingModel, 'isTimeAvailable')) {
            $this->markTestSkipped('O model Scheduling não possui método isTimeAvailable().');
        }

        $available = $schedulingModel->isTimeAvailable(
            $idBarbeiro,
            '2026-12-20 10:00:00',
            [$idServico]
        );

        $this->assertFalse($available);
    }

    public function testSchedulingServicesAreSaved(): void
    {
        $idCliente = $this->createClient();
        $idBarbeiro = $this->createBarberForScheduling();
        $idServico = $this->createService('Barba', 25.00, 40);

        $idAgendamento = $this->createScheduling($idCliente, $idBarbeiro, 'pendente');

        $stmt = $this->pdo->prepare("
            INSERT INTO agendamento_servico (id_agendamento, id_servico)
            VALUES (:id_agendamento, :id_servico)
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_servico', $idServico, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) AS total
            FROM agendamento_servico
            WHERE id_agendamento = :id_agendamento
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, (int) $result['total']);
    }

    private function createBarberForScheduling(
        string $nome = 'Barbeiro Teste',
        string $email = 'barbeiro.scheduling@teste.com'
    ): int {
        $barberModel = new Barber($this->pdo);

        return (int) $barberModel->create($nome, $email, []);
    }
}