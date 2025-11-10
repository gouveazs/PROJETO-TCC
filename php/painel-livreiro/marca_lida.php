<?php
include '../conexao.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    die('ID inválido');
}

$stmt = $conn->prepare("UPDATE notificacoes SET lida = 1 WHERE idnotificacoes = ?");
if ($stmt->execute([$id])) {
    echo 'ok';
} else {
    echo 'erro';
}
?>
