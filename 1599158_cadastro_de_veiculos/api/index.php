<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';

try {
    // === DB/MIGRATIONS ===
    $conn = getConnection();

    error_log("Iniciando verificação de migrations...");

    $stmt = $conn->query("SHOW TABLES LIKE 'migrations'");

    if ($stmt->rowCount() == 0) {

        error_log("Tabela migrations não encontrada. Criando...");

        require_once __DIR__ . "/migrations/000_create_migrations_table.php";

        if (function_exists('up_000_create_migrations_table')) {
            up_000_create_migrations_table($conn);
            error_log("Migration 000_create_migrations_table executada.");
        }
    }

    $files = glob(__DIR__ . "/migrations/*.php");
    sort($files);

    foreach ($files as $file) {
        $migrationName = basename($file);

        if ($migrationName === '000_create_migrations_table.php') {
            continue;
        }

        error_log("Verificando migration: " . $migrationName);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);

        if ($stmt->fetchColumn() == 0) {
            error_log("Executando migration: " . $migrationName);

            require_once $file;

            $functionName = 'up_' . str_replace('.php', '', $migrationName);

            if (function_exists($functionName)) {

                error_log("Chamando função: " . $functionName);

                $functionName($conn);

                $insert = $conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
                $insert->execute([$migrationName]);

                error_log("Migration registrada: " . $migrationName);
            } else {
                error_log("Função não encontrada: " . $functionName);
            }
        } else {
            error_log("Migration já executada: " . $migrationName);
        }
    }

    // === ROUTERS ===
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($uri === '/vehicle') {
        require_once 'routes/vehicle.php';
        exit;
    }

    http_response_code(404);
    echo json_encode(["error" => "Rota não encontrada"]);
} catch (PDOException $e) {

    error_log("Erro de banco: " . $e->getMessage());

    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
