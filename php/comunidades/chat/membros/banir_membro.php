<?php
session_start();
include '../../../conexao.php';

$id_requisitante = $_SESSION['idusuario'] ?? null;
$id_usuario = $_GET['id_usuario'] ?? null; // alvo
$id_comunidade = $_GET['id_comunidade'] ?? null;

if(!$id_requisitante || !$id_usuario || !$id_comunidade){
    die("Requisição inválida.");
}

// Pega quem é o criador (dono)
$stmt = $conn->prepare("SELECT idusuario AS id_criador FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([':id' => $id_comunidade]);
$com = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$com){
    die("Comunidade não encontrada.");
}

// Só o dono pode banir nesta tela
if($com['id_criador'] != $id_requisitante){
    die("Apenas o administrador da comunidade pode banir membros.");
}

// Não deixa banir o dono
if($id_usuario == $com['id_criador']){
    die("O dono da comunidade não pode ser banido.");
}

try {
    $conn->beginTransaction();

    // Insere banimento
    $stmt = $conn->prepare("
        INSERT INTO banimentos_comunidade (idcomunidades, idusuario, motivo)
        VALUES (:id_comunidade, :id_usuario, :motivo)
    ");
    $stmt->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario,
        ':motivo' => 'Banido pelo administrador'
    ]);

    // Remove dos membros
    $stmt = $conn->prepare("DELETE FROM membros_comunidade WHERE idcomunidades = :id AND idusuario = :user");
    $stmt->execute([':id' => $id_comunidade, ':user' => $id_usuario]);

    // Atualiza contador
    $stmt = $conn->prepare("
        UPDATE comunidades
        SET quantidade_usuarios = GREATEST(0, COALESCE(quantidade_usuarios, 0) - 1)
        WHERE idcomunidades = :id
    ");
    $stmt->execute([':id' => $id_comunidade]);

    $conn->commit();

    echo "<script>alert('Usuário banido com sucesso!'); window.location='ver_membros.php?id_comunidade={$id_comunidade}';</script>";

} catch (Exception $e) {
    $conn->rollBack();
    die("Erro ao banir usuário: " . $e->getMessage());
}
