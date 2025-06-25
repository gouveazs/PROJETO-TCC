<?php
include '../conexao.php';

// dados usuario
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

// verifica se ja existe o nome ou email cadastrado
$sqlCheck = "SELECT COUNT(*) FROM cadastro_usuario WHERE nome = :nome OR email = :email";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bindParam(':nome', $nome);
$stmtCheck->bindParam(':email', $email);
$stmtCheck->execute();
$existe = $stmtCheck->fetchColumn();

if ($existe > 0) {
    header("Location: ../cadastro/cadastroUsuario.php?erro=nome_ou_email");
    exit();
}

// foto de perfil
$foto_de_perfil = null;
if (isset($_FILES['foto_de_perfil']) && $_FILES['foto_de_perfil']['error'] == 0) {
    // Abra o arquivo de imagem em modo binário
    $foto_de_perfil = file_get_contents($_FILES['foto_de_perfil']['tmp_name']);
}

try {
    $sql = "INSERT INTO cadastro_usuario (nome, email, senha, foto_de_perfil) VALUES (:nome, :email, :senha, :foto)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':foto', $foto_de_perfil, PDO::PARAM_LOB);

    $stmt->execute();

    header("Location: ../../index.php?sucesso=cadastro_usuario");
    exit();

} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>