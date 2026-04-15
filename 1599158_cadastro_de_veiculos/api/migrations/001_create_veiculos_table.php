<?php

function up_001_create_veiculos_table($pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS veiculos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            placa VARCHAR(10) NOT NULL UNIQUE,
            marca VARCHAR(100) NOT NULL,
            modelo VARCHAR(100) NOT NULL,
            ano_fabricacao INT NOT NULL,
            ano_modelo INT NOT NULL,
            cor VARCHAR(50) NOT NULL,
            combustivel VARCHAR(50) NOT NULL,
            quilometragem INT NOT NULL,
            chassi VARCHAR(50) NOT NULL UNIQUE,
            renavam VARCHAR(50) NOT NULL UNIQUE,
            data_cadastro DATE NOT NULL,
            observacoes TEXT NOT NULL
        )
    ");
}
