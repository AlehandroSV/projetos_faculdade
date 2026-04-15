<?php

class VehicleController
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // GET /vehicle
    public function index()
    {

        $stmt = $this->conn->query("SELECT * FROM veiculos");
        $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($veiculos);
    }

    // POST /vehicle
    public function store()
    {

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            return;
        }

        $requiredFields = [
            'placa',
            'marca',
            'modelo',
            'ano_fabricacao',
            'ano_modelo',
            'cor',
            'combustivel',
            'quilometragem',
            'chassi',
            'renavam',
            'data_cadastro',
            'observacoes'
        ];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "Campo obrigatório ausente: $field";
            }
        }

        if (!is_numeric($data['ano_fabricacao'] ?? null)) {
            $errors[] = "ano_fabricacao deve ser número";
        }

        if (!is_numeric($data['ano_modelo'] ?? null)) {
            $errors[] = "ano_modelo deve ser número";
        }

        if (!is_numeric($data['quilometragem'] ?? null)) {
            $errors[] = "quilometragem deve ser número";
        }

        if (!strtotime($data['data_cadastro'] ?? '')) {
            $errors[] = "data_cadastro deve ser uma data válida (YYYY-MM-DD)";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["errors" => $errors]);
            return;
        }

        $sql = "INSERT INTO veiculos 
        (placa, marca, modelo, ano_fabricacao, ano_modelo, cor, combustivel, quilometragem, chassi, renavam, data_cadastro, observacoes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            $data['placa'],
            $data['marca'],
            $data['modelo'],
            (int)$data['ano_fabricacao'],
            (int)$data['ano_modelo'],
            $data['cor'],
            $data['combustivel'],
            (int)$data['quilometragem'],
            $data['chassi'],
            $data['renavam'],
            $data['data_cadastro'],
            $data['observacoes']
        ]);

        http_response_code(201);

        echo json_encode([
            "message" => "Veículo criado com sucesso"
        ]);
    }
}
