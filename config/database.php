
<?php

// Configurações do banco de dados

$host = 'localhost';
$banco = 'papertech';
$usuario = 'root';
$senha = '';

// Opções de conexão PDO

$opcoes = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {

    // Criando a conexão

    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        $opcoes
    );

} catch (PDOException $erro) {

    die("Erro ao conectar ao banco de dados.");

}

?>
