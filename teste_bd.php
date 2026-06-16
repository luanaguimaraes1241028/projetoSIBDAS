<?php
try {
    $ligacao = new PDO(
        "mysql:host=vsgate-s1.dei.isep.ipp.pt;port=10464;dbname=db1241028;charset=utf8",
        "db1241028",
        "3LduNkJe55lVk0iaQRXvV0j1tZpA7OW5"
    );
    echo "Ligação bem sucedida!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
