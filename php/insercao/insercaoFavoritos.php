<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['idusuario'])) {
    header("Location: ../login/login.php");
    exit();
}

$idusuario = $_SESSION['idusuario'];
$idproduto = $_POST['idproduto'];

if (empty($idproduto)) {
    die("Produto não informado.");
}

try {
    $sql = "INSERT INTO favoritos (idusuario, idproduto) VALUES (:idusuario, :idproduto)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idusuario', $idusuario);
    $stmt->bindParam(':idproduto', $idproduto);
    $stmt->execute();

    header("Location: ../produto/pagiproduto.php?id=$idproduto");
    exit();

} catch (PDOException $e) {
    echo "Erro ao inserir: " . $e->getMessage();
}
?>
