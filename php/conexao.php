<?php

$db = 1;

if ( $db == 1) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banco";
} else {
    $servername = "localhost";
    $username = "u557720587_2025_php03";
    $password = "Mtec@php3";
    $dbname = "u557720587_2025_php03";
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conectado com sucesso!";
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
    die();
}
?>
