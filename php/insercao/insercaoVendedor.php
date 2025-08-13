<?php
include '../conexao.php';

//dados vendedor
$nome_completo = $_POST['nomeV'];
$data_nascimento = $_POST['data_nascimento'];
$email_vendedor = $_POST['emailV'];
$senha_vendedor = $_POST['senhaV'];
$cpf = $_POST['cpf'];
$cnpj = $_POST['cnpj'];

// verifica se ja existe o nome ou email cadastrado
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

// foto de perfil
$foto_de_perfil = null;
if (isset($_FILES['foto_de_perfil']) && $_FILES['foto_de_perfil']['error'] == 0) {
    // Abra o arquivo de imagem em modo binário
    $foto_de_perfil = file_get_contents($_FILES['foto_de_perfil']['tmp_name']);
}

try {
    $sql = "INSERT INTO cadastro_vendedor (nome_completo, data_nascimento, email, senha, cpf, cnpj) VALUES ('$nome_completo','$data_nascimento','$email_vendedor','$senha_vendedor','$cpf','$cnpj')";
    $conn -> exec($sql); 

    echo "Boa:";
    header("Location: ../login/loginVendedor.php?sucesso=cadastro_usuario");
    exit();

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>