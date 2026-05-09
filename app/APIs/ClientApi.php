<?php
// app/APIs/ClientApi.php

require_once __DIR__ . '/../../config/database.php'; // Ajuste o caminho se necessário
require_once __DIR__ . '/../controllers/UserController.php';

// 1. Conexão (Supondo que seu config/database.php retorna um $pdo ou tem uma classe)
// Se for classe: $database = new Database(); $pdo = $database->getConnection();
// Vou usar a variável $pdo que deve vir do seu config:
require_once __DIR__ . '/../../config/database.php'; 

$controller = new UserController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

$controller->handleAPI($method, $id);