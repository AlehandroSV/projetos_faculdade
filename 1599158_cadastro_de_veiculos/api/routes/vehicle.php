<?php

require_once __DIR__ . '/../controllers/VehicleController.php';

$controller = new VehicleController($conn);

if ($method === 'GET') {
    $controller->index();
    return;
}

if ($method === 'POST') {
    $controller->store();
    return;
}

http_response_code(405);
echo json_encode(["error" => "Método não permitido"]);
