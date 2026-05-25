<?php

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../app/models/Service.php';

class ServiceTest extends TestCase
{
    public function testListServices(): void
    {
        $this->createService('Barba', 25.00, 40);
        $this->createService('Cabelo', 35.00, 50);

        $serviceModel = new Service($this->pdo);

        if (method_exists($serviceModel, 'all')) {
            $services = $serviceModel->all();
        } elseif (method_exists($serviceModel, 'getAll')) {
            $services = $serviceModel->getAll();
        } else {
            $this->markTestSkipped('O model Service não possui método all() ou getAll().');
        }

        $this->assertIsArray($services);
        $this->assertCount(2, $services);
    }

    public function testFindServiceById(): void
    {
        $idServico = $this->createService('Barba', 25.00, 40);

        $serviceModel = new Service($this->pdo);

        if (method_exists($serviceModel, 'find')) {
            $service = $serviceModel->find($idServico);
        } elseif (method_exists($serviceModel, 'getById')) {
            $service = $serviceModel->getById($idServico);
        } else {
            $this->markTestSkipped('O model Service não possui método find() ou getById().');
        }

        $this->assertNotFalse($service);
        $this->assertEquals('Barba', $service['nome']);
        $this->assertArrayHasKey('descricao', $service);
        $this->assertEquals('25.00', number_format((float) $service['preco'], 2, '.', ''));
    }

    public function testFindNonexistentServiceReturnsFalse(): void
    {
        $serviceModel = new Service($this->pdo);

        if (method_exists($serviceModel, 'find')) {
            $service = $serviceModel->find(9999);
        } elseif (method_exists($serviceModel, 'getById')) {
            $service = $serviceModel->getById(9999);
        } else {
            $this->markTestSkipped('O model Service não possui método find() ou getById().');
        }

        $this->assertFalse($service);
    }

    public function testCreateServiceIfMethodExists(): void
    {
        $serviceModel = new Service($this->pdo);

        if (!method_exists($serviceModel, 'create')) {
            $this->markTestSkipped('O model Service não possui método create().');
        }

        $result = $serviceModel->create([
            'nome' => 'Sobrancelha',
            'descricao' => 'Design de sobrancelha',
            'preco' => 15.00,
            'duracao' => 20
        ]);

        $this->assertNotFalse($result);

        $stmt = $this->pdo->query("SELECT * FROM servico WHERE nome = 'Sobrancelha' LIMIT 1");
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($service);
        $this->assertEquals('Sobrancelha', $service['nome']);
        $this->assertEquals('Design de sobrancelha', $service['descricao']);
        $this->assertEquals('15.00', number_format((float) $service['preco'], 2, '.', ''));
        $this->assertEquals(20, (int) $service['duracao']);
    }

    public function testUpdateServiceIfMethodExists(): void
    {
        $idServico = $this->createService('Barba', 25.00, 40);

        $serviceModel = new Service($this->pdo);

        if (!method_exists($serviceModel, 'update')) {
            $this->markTestSkipped('O model Service não possui método update().');
        }

        $result = $serviceModel->update($idServico, [
            'nome' => 'Barba Completa',
            'descricao' => 'Barba completa com acabamento',
            'preco' => 30.00,
            'duracao' => 45
        ]);

        $this->assertNotFalse($result);

        $stmt = $this->pdo->prepare("SELECT * FROM servico WHERE id_servico = :id");
        $stmt->bindValue(':id', $idServico, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Barba Completa', $service['nome']);
        $this->assertEquals('Barba completa com acabamento', $service['descricao']);
        $this->assertEquals('30.00', number_format((float) $service['preco'], 2, '.', ''));
        $this->assertEquals(45, (int) $service['duracao']);
    }

    public function testDeleteServiceIfMethodExists(): void
    {
        $idServico = $this->createService('Barba', 25.00, 40);

        $serviceModel = new Service($this->pdo);

        if (!method_exists($serviceModel, 'delete')) {
            $this->markTestSkipped('O model Service não possui método delete().');
        }

        $result = $serviceModel->delete($idServico);

        $this->assertNotFalse($result);

        $stmt = $this->pdo->prepare("SELECT * FROM servico WHERE id_servico = :id");
        $stmt->bindValue(':id', $idServico, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertFalse($service);
    }
}