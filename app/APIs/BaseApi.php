<?php

class BaseApi
{
    protected function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function json($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth()
    {
        $this->startSessionIfNeeded();

        if (empty($_SESSION['id_cliente'])) {
            $this->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }
    }
}