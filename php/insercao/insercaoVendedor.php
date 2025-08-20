<?php
include '../conexao.php';

// Dados do vendedor
$nome_completo = $_POST['nomeV'];
$data_nascimento = $_POST['data_nascimento'];
$email_vendedor = $_POST['emailV'];
$senha_vendedor = $_POST['senhaV'];
$cpf = $_POST['cpf'];
$cnpj = $_POST['cnpj'];

// Verifica se já existe o nome ou email cadastrado
$sqlCheck = "SELECT COUNT(*) FROM cadastro_vendedor WHERE nome_completo = :nome_completo OR email = :email";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bindParam(':nome_completo', $nome_completo);
$stmtCheck->bindParam(':email', $email_vendedor);
$stmtCheck->execute();
$existe = $stmtCheck->fetchColumn();

if ($existe > 0) {
    header("Location: ../cadastro/cadastroVendedor.php?erro=nome_ou_email");
    exit();
}

// Foto de perfil
$foto_de_perfil = null;
if (isset($_FILES['foto_de_perfil']) && $_FILES['foto_de_perfil']['error'] == 0) {
    $foto_de_perfil = file_get_contents($_FILES['foto_de_perfil']['tmp_name']);
}

try {
    $sql = "INSERT INTO cadastro_vendedor (nome_completo, data_nascimento, email, senha, cpf, cnpj, foto_de_perfil) VALUES 
            (:nome_completo, :data_nascimento, :email, :senha, :cpf, :cnpj, :foto_de_perfil)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome_completo', $nome_completo);
    $stmt->bindParam(':data_nascimento', $data_nascimento);
    $stmt->bindParam(':email', $email_vendedor);
    $stmt->bindParam(':senha', $senha_vendedor);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':foto_de_perfil', $foto_de_perfil, PDO::PARAM_LOB);

    $stmt->execute();

    header("Location: ../login/loginVendedor.php");
    exit();

} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>