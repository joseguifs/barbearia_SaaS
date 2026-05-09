<?php

require_once __DIR__ . '/../models/Client.php';

class ClientController
{
    private $clientModel;

    public function __construct($pdo)
    {
        $this->clientModel = new Client($pdo);
    }

    public function handleAPI($method, $id = null)
    {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        switch ($method) {
            case 'GET':
                echo json_encode($id ? $this->clientModel->find($id) : $this->clientModel->all());
                break;
                
            case 'POST':
                if (empty($data['senha'])) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "A senha é obrigatória."]);
                    break;
                }
                $senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT);
                $emailFinal = empty($data['email']) ? null : $data['email'];
                
                $sucesso = $this->clientModel->create($data['nome'], $data['telefone'], $emailFinal, $senhaHash);
                echo json_encode(["success" => $sucesso]);
                break;
                
            case 'PUT':
                $emailFinal = empty($data['email']) ? null : $data['email'];
                $sucesso = $this->clientModel->update($id, $data['nome'], $data['telefone'], $emailFinal);
                echo json_encode(["success" => $sucesso]);
                break;
                
            case 'DELETE':
                $sucesso = $this->clientModel->delete($id);
                echo json_encode(["success" => $sucesso]);
                break;
                
            default:
                http_response_code(405);
                echo json_encode(["message" => "Método não permitido"]);
                break;
        }
    }
}