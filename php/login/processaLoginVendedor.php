<?php
include '../conexao.php';
session_start();

$nome_completo = htmlspecialchars($_POST['nome_completo']);
$senha = $_POST['senha']; 

try {
    $stmt = $conn->prepare("SELECT * FROM vendedor WHERE nome_completo = ?");
    $stmt->execute([$nome_completo]);
    $vendedor_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vendedor_db && password_verify($senha, $vendedor_db['senha'])) {
        if ($vendedor_db['status'] === 'desativado') {
            header('Location: loginVendedor.php?error=2&vendedor=' . urlencode($nome_completo));
            exit();
        }

        $_SESSION['id_vendedor'] = $vendedor_db['idvendedor'];
        $_SESSION['nome_vendedor'] = $vendedor_db['nome_completo'];
        $_SESSION['foto_de_perfil_vendedor'] = $vendedor_db['foto_de_perfil'];

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
