<?php
include '../../conexao.php';

$id_comunidade = $_GET['id_comunidade'] ?? null;
if (!$id_comunidade || !ctype_digit((string)$id_comunidade)) {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT m.idmensagens_chat, m.mensagem, m.enviada_em, u.nome, u.foto_de_perfil, mc.papel
    FROM mensagens_comunidade m
    JOIN usuario u ON m.idusuario = u.idusuario
    JOIN membros_comunidade mc ON mc.idusuario = u.idusuario AND mc.idcomunidades = m.idcomunidades
    WHERE m.idcomunidades = :id
    ORDER BY m.enviada_em ASC
");
$stmt->execute([':id' => $id_comunidade]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converter imagem para base64 (se nula, devolve string vazia)
foreach ($mensagens as &$msg) {
    $msg['idmensagens_chat'] = isset($msg['idmensagens_chat']) ? (int)$msg['idmensagens_chat'] : null;
    $msg['foto_de_perfil'] = $msg['foto_de_perfil'] !== null ? base64_encode($msg['foto_de_perfil']) : '';
    // opcional: formatar data para string legível
    // $msg['enviada_em'] = date('d/m/Y H:i', strtotime($msg['enviada_em']));
}

header('Content-Type: application/json');
echo json_encode($mensagens);
