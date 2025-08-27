<?php
include '../conexao.php';

session_start();

$nome_completo = htmlspecialchars($_POST['nome_completo']);
$senha = htmlspecialchars($_POST['senha']);

try {
    $stmt = $conn->prepare("SELECT * FROM cadastro_vendedor WHERE nome_completo = ? AND senha = ?");
    $stmt->execute([$nome_completo, $senha]);
    $vendedor_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vendedor_db) {
        $_SESSION['id_vendedor'] = $vendedor_db['id_vendedor'];
        $_SESSION['nome_vendedor'] = $vendedor_db['nome_completo'];
        $_SESSION['foto_de_perfil-vendedor'] = $vendedor_db['foto_de_perfil'];

        header('Location: ../painel-livreiro/painel_livreiro.php');
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