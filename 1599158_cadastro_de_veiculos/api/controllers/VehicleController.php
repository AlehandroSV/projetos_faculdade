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
            'data_cadastro'
        ];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "Campo obrigatório ausente: $field";
            }
        }

        if (isset($data['ano_fabricacao']) && !is_numeric($data['ano_fabricacao'])) {
            $errors[] = "ano_fabricacao deve ser número";
        }

        if (isset($data['ano_modelo']) && !is_numeric($data['ano_modelo'])) {
            $errors[] = "ano_modelo deve ser número";
        }

        if (isset($data['quilometragem']) && !is_numeric($data['quilometragem'])) {
            $errors[] = "quilometragem deve ser número";
        }

        if (isset($data['data_cadastro']) && !strtotime($data['data_cadastro'])) {
            $errors[] = "data_cadastro deve ser uma data válida (YYYY-MM-DD)";
        }

        if (isset($data['placa']) && !preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', strtoupper($data['placa']))) {
            $errors[] = "placa inválida";
        }

        $anoAtual = date("Y");

        if (isset($data['ano_fabricacao']) && ($data['ano_fabricacao'] < 1900 || $data['ano_fabricacao'] > $anoAtual + 1)) {
            $errors[] = "ano_fabricacao inválido";
        }

        if (isset($data['ano_modelo']) && ($data['ano_modelo'] < 1900 || $data['ano_modelo'] > $anoAtual + 1)) {
            $errors[] = "ano_modelo inválido";
        }

        if (isset($data['quilometragem']) && $data['quilometragem'] < 0) {
            $errors[] = "quilometragem não pode ser negativa";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["errors" => $errors]);
            return;
        }

        $check = $this->conn->prepare("SELECT id FROM veiculos WHERE placa = ?");
        $check->execute([strtoupper($data['placa'])]);

        if ($check->fetch()) {
            http_response_code(409);
            echo json_encode([
                "error" => "Já existe um veículo com essa placa"
            ]);
            return;
        }

        $sql = "INSERT INTO veiculos 
            (placa, marca, modelo, ano_fabricacao, ano_modelo, cor, combustivel, quilometragem, chassi, renavam, data_cadastro, observacoes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                strtoupper($data['placa']),
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
                $data['observacoes'] ?? null
            ]);

            http_response_code(201);

            echo json_encode([
                "message" => "Veículo criado com sucesso"
            ]);
        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {
                http_response_code(409);
                echo json_encode([
                    "error" => "Registro duplicado (placa, chassi ou renavam já cadastrado)"
                ]);
                return;
            }

            http_response_code(500);
            echo json_encode([
                "error" => "Erro interno ao salvar veículo"
            ]);
        }
    }
}
