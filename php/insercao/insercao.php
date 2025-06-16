<?php
include '../conexao.php';

// dados usuario
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

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
    $stmt->bindParam(':foto', $fotoBin, PDO::PARAM_LOB);

    $stmt->execute();

    header("Location: ../consulta/consulta.php");
    exit();

} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>