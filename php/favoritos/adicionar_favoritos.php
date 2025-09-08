<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['idusuario'])) {
    header("Location: ../login/login.php");
    exit();
}

$idusuario = $_SESSION['idusuario'];
$idproduto = $_POST['idproduto'];

try {
    $sql = "INSERT INTO favoritos (idusuario, idproduto) VALUES (:idusuario, :idproduto)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':idusuario' => $idusuario,
        ':idproduto' => $idproduto
    ]);

    header("Location: produto.php?id=$idproduto&msg=favorito_adicionado");
    exit();

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // entrada duplicada
        header("Location: ../../index.php");
    } else {
        die("Erro: " . $e->getMessage());
    }
}
