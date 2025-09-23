<?php
include '../conexao.php';

if (isset($_GET['usuario'])) {
    $usuario = $_GET['usuario'];
    $stmt = $conn->prepare("UPDATE usuario SET status = 'ativo' WHERE nome = ?");
    $stmt->execute([$usuario]);

    header('Location: login.php?reativado=1');
    exit();
} else {
    header('Location: login.php');
    exit();
}
