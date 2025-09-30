<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'];
$id_comunidade = $_POST['id_comunidade'];
$mensagem = trim($_POST['mensagem']);

if (!empty($mensagem)) {
    $stmt = $conn->prepare("
        INSERT INTO mensagens_chat (idcomunidades, idusuario, mensagem)
        VALUES (:id_comunidade, :id_usuario, :mensagem)
    ");
    $stmt->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario,
        ':mensagem' => $mensagem
    ]);
}

echo "ok";
