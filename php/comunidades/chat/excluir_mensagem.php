<?php
header('Content-Type: application/json');
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_comunidade = $_POST['id_comunidade'] ?? null;
$id_mensagem = $_POST['id_mensagem'] ?? null;

// validações
if (!$id_usuario || !$id_comunidade || !$id_mensagem || !ctype_digit((string)$id_comunidade) || !ctype_digit((string)$id_mensagem)) {
    echo json_encode(["sucesso" => false, "mensagem" => "Dados inválidos."]);
    exit;
}

$id_comunidade = (int)$id_comunidade;
$id_mensagem = (int)$id_mensagem;

// Pega o papel do usuário na comunidade
$stmt = $conn->prepare("SELECT papel FROM membros_comunidade WHERE idcomunidades = :com AND idusuario = :user");
$stmt->execute([':com' => $id_comunidade, ':user' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || ($usuario['papel'] !== 'moderador' && $usuario['papel'] !== 'dono')) {
    echo json_encode(["sucesso" => false, "mensagem" => "Você não tem permissão para excluir mensagens."]);
    exit;
}

// Exclui a mensagem (apenas dentro da comunidade)
$stmt = $conn->prepare("
    DELETE FROM mensagens_comunidade 
    WHERE idmensagens_chat = :id AND idcomunidades = :com
");
$stmt->execute([
    ':id' => $id_mensagem,
    ':com' => $id_comunidade
]);

echo json_encode(["sucesso" => true, "mensagem" => "Mensagem excluída com sucesso!"]);
