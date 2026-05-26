<?php

require_once __DIR__ . '/../models/Barber.php';
require_once __DIR__ . '/../models/Scheduling.php';

class BarberController
{
    private $barberModel;
    private $schedulingModel;

    public function __construct($pdo)
    {
        $this->barberModel = new Barber($pdo);
        $this->schedulingModel = new Scheduling($pdo);
    }

    public function profile()
    {
        // Pega o ID da URL para testes (ex: index.php?action=barber_profile&id=1)
        // No futuro, você substituirá isso pelo $_SESSION['id_barbeiro'] quando fizer o login do barbeiro.
        $idBarbeiro = $_GET['id'] ?? 1; 
        
        $dadosBarbeiro = $this->barberModel->find($idBarbeiro);
        
        if (!$dadosBarbeiro) {
            echo "Barbeiro não encontrado.";
            return;
        }

        // Busca a agenda do dia atual
        $hoje = date('Y-m-d');
        $agendamentos = $this->schedulingModel->getDailyAgenda($idBarbeiro, $hoje);

        // Renderiza a view
        require_once __DIR__ . '/../views/barber/profile.php';
    }
}