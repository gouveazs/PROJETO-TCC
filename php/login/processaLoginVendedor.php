<?php
include '../conexao.php';

session_start();

$nome_completo = htmlspecialchars($_POST['nome_completo']);
$senha = htmlspecialchars($_POST['senha']);

try {
    $stmt = $conn->prepare("SELECT * FROM cadastro_vendedor WHERE nome_completo = ? AND senha = ?");
    $stmt->execute([$nome_completo, $senha]);
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_db) {
        $_SESSION['id_vendedor'] = $usuario_db['idvendedor'];
        $_SESSION['nome_vendedor'] = $usuario_db['nome_completo'];
        $_SESSION['foto_de_perfil'] = $usuario_db['foto_de_perfil'] ?? 'imgs/usuario.jpg';

        header('Location: ../perfil-vendedor/perfil-vendedor.php');
        exit();
    } else {
        header('Location: loginVendedor.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    die();
}
?>