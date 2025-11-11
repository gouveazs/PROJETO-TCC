<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['id_vendedor'])) {
    die("Acesso negado.");
}

$iditem = $_POST['iditem_pedido'] ?? null;
$status = $_POST['status_envio'] ?? 'aguardando envio';
$rastreio = $_POST['codigo_rastreio_item'] ?? null;

if (!$iditem) {
    die("Item inválido.");
}

$stmt = $conn->prepare("
    UPDATE item_pedido
    SET status_envio = :status, codigo_rastreio_item = :rastreio
    WHERE iditem_pedido = :iditem
");
$stmt->execute([
    ':status' => $status,
    ':rastreio' => $rastreio,
    ':iditem' => $iditem
]);

header('Location: pedidos.php');
exit;
