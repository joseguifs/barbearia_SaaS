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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_barbeiro'])) {
            header('Location: index.php?action=barber_login');
            exit;
        }

        $idBarbeiro = (int) $_SESSION['id_barbeiro'];

        $dadosBarbeiro = $this->barberModel->find($idBarbeiro);

        if (!$dadosBarbeiro) {
            echo "Barbeiro não encontrado.";
            return;
        }

        $hoje = date('Y-m-d');
        $hojeFormatado = date('d/m/Y');

        $agendamentos = $this->schedulingModel->getDailyAgenda($idBarbeiro, $hoje);

        require_once __DIR__ . '/../views/barber/profile.php';
    }
}
