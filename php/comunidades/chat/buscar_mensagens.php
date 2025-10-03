<?php
include '../../conexao.php';

$id_comunidade = $_GET['id_comunidade'];

$stmt = $conn->prepare("
    SELECT m.mensagem, m.enviada_em, u.nome, u.foto_de_perfil, mc.papel
    FROM mensagens_comunidade m
    JOIN usuario u ON m.idusuario = u.idusuario
    JOIN membros_comunidade mc ON mc.idusuario = u.idusuario AND mc.idcomunidades = m.idcomunidades
    WHERE m.idcomunidades = :id
    ORDER BY m.enviada_em ASC
");
$stmt->execute([':id' => $id_comunidade]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converter imagem para base64
foreach ($mensagens as &$msg) {
    $msg['foto_de_perfil'] = base64_encode($msg['foto_de_perfil']);
}

header('Content-Type: application/json');
echo json_encode($mensagens);
