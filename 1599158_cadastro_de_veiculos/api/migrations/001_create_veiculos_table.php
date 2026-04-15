<?php

function up($pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS veiculos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            placa VARCHAR(10) NOT NULL,
            marca VARCHAR(100) NOT NULL,
            modelo VARCHAR(100) NOT NULL,
            ano_fabricacao INT NOT NULL,
            ano_modelo INT NOT NULL,
            cor VARCHAR(50),
            combustivel VARCHAR(50),
            quilometragem INT,
            chassi VARCHAR(50),
            renavam VARCHAR(50),
            data_cadastro DATE,
            observacoes TEXT
        )
    ");
}
