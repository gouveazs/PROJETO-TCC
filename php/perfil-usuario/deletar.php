<?php
session_start();
include '../conexao.php'; 

$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;


if (!$nome_usuario) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE nome = ?");
$stmt->execute([$nome_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$idusuario = isset($usuario['idusuario']) ? $usuario['idusuario'] : null;


if (!$idusuario) {
    echo "Usuário não encontrado.";
    exit;
}

// expurgar o usuario da terra
$stmt = $conn->prepare("UPDATE usuario SET status = 'desativado' WHERE idusuario = ?");
if ($stmt->execute([$idusuario])) {
    session_destroy();
    echo "<img src='https://media.tenor.com/URShj_JWaS8AAAAM/explos%C3%A3o-meme.gif' />";
    echo "<br>";
    echo "<a href='../../index.php'>Voltar à página inicial</a>";
} else {
    echo "<p style='color:red;'>Erro ao deletar conta.</p>";
}
?>
