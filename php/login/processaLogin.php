<?php
include '../conexao.php';

$usuario = htmlspecialchars($_POST['usuario']);
$senha = $_POST['senha']; // não precisa do htmlspecialchars aqui

try {
    // busca só pelo nome, sem comparar senha direto
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE nome = ?");
    $stmt->execute([$usuario]);
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_db && password_verify($senha, $usuario_db['senha'])) {
        if ($usuario_db['status'] === 'desativado') {
            header('Location: login.php?error=2&usuario=' . urlencode($usuario));
            exit();
        }

        session_start();
        $_SESSION['idusuario'] = $usuario_db['idusuario'];
        $_SESSION['nome_usuario'] = $usuario_db['nome']; 
        $_SESSION['foto_de_perfil'] = $usuario_db['foto_de_perfil'];

        if ($usuario === 'adm') {
            header('Location: ../pag-adm/adm.php');
        } else {
            header('Location: ../../index.php');
        }
        exit();
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    die();
}