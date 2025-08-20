<?php

include '../conexao.php';

$usuario = htmlspecialchars($_POST['usuario']);
$senha = htmlspecialchars($_POST['senha']);

try {
    $stmt = $conn->prepare("SELECT * FROM cadastro_usuario WHERE nome = ? AND senha = ?");
    $stmt->execute([$usuario, $senha]);
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_db) {
        session_start();
        $_SESSION['usuario_logado'] = $usuario_db['id'];
        $_SESSION['nome_usuario'] = $usuario_db['nome']; 
        $_SESSION['foto_de_perfil'] = $usuario_db['foto_de_perfil']; 
        header('Location: ../../index.php');
        exit();
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    die();
}
