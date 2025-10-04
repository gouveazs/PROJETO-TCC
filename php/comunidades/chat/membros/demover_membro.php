<?php
session_start();
include '../../../conexao.php';

$id_admin = $_SESSION['idusuario'] ?? null;
$id_usuario = $_GET['id_usuario'] ?? null;
$id_comunidade = $_GET['id_comunidade'] ?? null;

if(!$id_admin || !$id_usuario || !$id_comunidade){
    die("Dados inválidos.");
}

// Verifica se admin é dono
$stmt = $conn->prepare("SELECT idusuario FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([":id" => $id_comunidade]);
$com = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$com || $com['idusuario'] != $id_admin){
    die("Apenas o dono pode demover.");
}

// Atualiza o papel do usuário para membro
$stmt = $conn->prepare("UPDATE membros_comunidade 
                        SET papel = 'membro' 
                        WHERE idcomunidades = :com AND idusuario = :user");
$stmt->execute([":com" => $id_comunidade, ":user" => $id_usuario]);

// Redireciona de volta pra página de membros
header("Location: ver_membros.php?id_comunidade=$id_comunidade&msg=demovido");
exit;
