<?php
include '../conexao.php';

$idpedido = $_POST['idpedido'] ?? null;
$status_envio = $_POST['status_envio'] ?? null;
$codigo_rastreio = $_POST['codigo_rastreio'] ?? null;

if (!$idpedido || !$status_envio) {
    die("Dados inválidos.");
}

$stmt = $conn->prepare("
    UPDATE pedido
    SET status_envio = :status_envio,
        codigo_rastreio = :codigo_rastreio
    WHERE idpedido = :idpedido
");
$stmt->bindValue(':status_envio', $status_envio);
$stmt->bindValue(':codigo_rastreio', $codigo_rastreio);
$stmt->bindValue(':idpedido', $idpedido, PDO::PARAM_INT);
$stmt->execute();

header("Location: pedidos.php");
exit;
?>
