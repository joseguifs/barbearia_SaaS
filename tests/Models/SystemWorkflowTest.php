<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/models/Barber.php';

class SystemWorkflowTest extends TestCase
{
    public function testCompleteSchedulingWorkflow(): void
    {
        $idCliente = $this->createClient('Cliente Fluxo');

        $idServico = $this->createService(
            'Corte Completo',
            45.00,
            60,
            'Corte de cabelo completo'
        );

        $barberModel = new Barber($this->pdo);

        $idBarbeiro = $barberModel->create(
            'Barbeiro Fluxo',
            'barbeiro.fluxo@teste.com',
            [$idServico]
        );

        $barber = $barberModel->findWithServices($idBarbeiro);

        $this->assertNotFalse($barber);
        $this->assertEquals('Barbeiro Fluxo', $barber['nome']);
        $this->assertEquals('barbeiro.fluxo@teste.com', $barber['email']);
        $this->assertCount(1, $barber['servicos']);
        $this->assertEquals('Corte Completo', $barber['servicos'][0]['nome']);

        $idAgendamento = $this->createScheduling(
            $idCliente,
            $idBarbeiro,
            'pendente'
        );

        $stmt = $this->pdo->prepare("
            SELECT 
                a.id_agendamento,
                a.id_cliente,
                a.id_barbeiro,
                a.status,
                c.nome AS cliente_nome,
                b.nome AS barbeiro_nome
            FROM agendamento a
            INNER JOIN cliente c
                ON c.id_cliente = a.id_cliente
            INNER JOIN barbeiro b
                ON b.id_barbeiro = a.id_barbeiro
            WHERE a.id_agendamento = :id_agendamento
            LIMIT 1
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($agendamento);
        $this->assertEquals($idCliente, $agendamento['id_cliente']);
        $this->assertEquals($idBarbeiro, $agendamento['id_barbeiro']);
        $this->assertEquals('pendente', $agendamento['status']);
        $this->assertEquals('Cliente Fluxo', $agendamento['cliente_nome']);
        $this->assertEquals('Barbeiro Fluxo', $agendamento['barbeiro_nome']);
    }

    public function testDeleteBarberReassignsSchedulingInCompleteWorkflow(): void
    {
        $idCliente = $this->createClient('Cliente Redirecionamento');

        $barberModel = new Barber($this->pdo);

        $idBarbeiroOriginal = $barberModel->create(
            'Barbeiro Original',
            'original@teste.com',
            []
        );

        $idBarbeiroSubstituto = $barberModel->create(
            'Barbeiro Substituto',
            'substituto@teste.com',
            []
        );

        $idAgendamento = $this->createScheduling(
            $idCliente,
            $idBarbeiroOriginal,
            'agendado'
        );

        $result = $barberModel->delete($idBarbeiroOriginal);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['reassigned_scheduling_count']);
        $this->assertEquals($idBarbeiroSubstituto, $result['new_barber']['id_barbeiro']);

        $stmt = $this->pdo->prepare("
            SELECT id_barbeiro, status
            FROM agendamento
            WHERE id_agendamento = :id_agendamento
            LIMIT 1
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($idBarbeiroSubstituto, $agendamento['id_barbeiro']);
        $this->assertEquals('pendente', $agendamento['status']);

        $barbeiroExcluido = $barberModel->find($idBarbeiroOriginal);

        $this->assertFalse($barbeiroExcluido);
    }

    public function testServiceBarberAndSchedulingRelationsAreConsistent(): void
    {
        $idCliente = $this->createClient('Cliente Relações');

        $idServico1 = $this->createService(
            'Barba',
            25.00,
            40,
            'Serviço de barba'
        );

        $idServico2 = $this->createService(
            'Cabelo',
            35.00,
            50,
            'Serviço de cabelo'
        );

        $barberModel = new Barber($this->pdo);

        $idBarbeiro = $barberModel->create(
            'Barbeiro Relações',
            'relacoes@teste.com',
            [$idServico1, $idServico2]
        );

        $idAgendamento = $this->createScheduling(
            $idCliente,
            $idBarbeiro,
            'pendente'
        );

        $stmt = $this->pdo->prepare("
            INSERT INTO agendamento_servico (id_agendamento, id_servico)
            VALUES 
                (:id_agendamento_1, :id_servico_1),
                (:id_agendamento_2, :id_servico_2)
        ");

        $stmt->bindValue(':id_agendamento_1', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_servico_1', $idServico1, PDO::PARAM_INT);
        $stmt->bindValue(':id_agendamento_2', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_servico_2', $idServico2, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->pdo->prepare("
            SELECT 
                s.nome
            FROM agendamento_servico ags
            INNER JOIN servico s
                ON s.id_servico = ags.id_servico
            WHERE ags.id_agendamento = :id_agendamento
            ORDER BY s.nome ASC
        ");

        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->execute();

        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $servicos);

        $nomes = array_column($servicos, 'nome');

        $this->assertContains('Barba', $nomes);
        $this->assertContains('Cabelo', $nomes);
    }
}