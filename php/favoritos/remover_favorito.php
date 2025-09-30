<?php
session_start();
include '../conexao.php';

$idusuario = $_SESSION['idusuario'] ?? null;
$idproduto = $_GET['idproduto'] ?? null;

if (!$idusuario || !$idproduto) {
    header('Location: favoritos.php'); // ou página de favoritos
    exit;
}

// Deleta o favorito
$stmt = $conn->prepare("DELETE FROM favoritos WHERE idusuario = :idusuario AND idproduto = :idproduto");
$stmt->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
$stmt->bindValue(':idproduto', $idproduto, PDO::PARAM_INT);
$stmt->execute();

header('Location: favoritos.php');
exit;
?>
