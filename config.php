<?php
$servername = "127.0.0.1";
$username = "root";
$password = "1234";
$dbname = "projeto_eventos01";

// Cria a conexão
$conexao = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>
