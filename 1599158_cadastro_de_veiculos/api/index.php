<?php

require_once 'config/database.php';

try {

    $conn = getConnection();

    $stmt = $conn->query("SHOW TABLES LIKE 'migrations'");

    if ($stmt->rowCount() == 0) {
        require __DIR__ . "/migrations/000_create_migrations_table.php";
        up($conn);
    }

    $files = glob(__DIR__ . "/migrations/*.php");

    foreach ($files as $file) {

        $migrationName = basename($file);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);

        if ($stmt->fetchColumn() == 0) {

            require $file;

            if (function_exists('up')) {
                up($conn);
            }

            $insert = $conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $insert->execute([$migrationName]);
        }
    }

    echo "Conectado com sucesso 🚀";
} catch (PDOException $e) {
    echo "Erro ao iniciar aplicação: " . $e->getMessage();
}
