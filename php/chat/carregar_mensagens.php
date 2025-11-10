<?php
include '../conexao.php';

header('Content-Type: application/json; charset=utf-8');

$idconversa = $_GET['idconversa'] ?? null;

if (!$idconversa) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        m.idmensagens,
        m.remetente_tipo,
        m.conteudo,
        m.data_envio
    FROM mensagens_chat m
    WHERE m.idconversa = ?
    ORDER BY m.data_envio ASC
");
$stmt->execute([$idconversa]);

$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($mensagens);
?>
