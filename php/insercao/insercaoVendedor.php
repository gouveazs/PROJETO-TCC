<?php
include '../conexaoVendedor.php';

//dados vendedor
$nome_completo = $_POST['nomeV'];
$idade = $_POST['idade'];
$email_vendedor = $_POST['emailV'];
$senha_vendedor = $_POST['senhaV'];
$cpf = $_POST['cpf'];
$cnpj = $_POST['cnpj'];

try {
    $sql = "INSERT INTO cadastro_vendedor (nome_completo, idade, email, senha, cpf, cnpj) VALUES ('$nome_completo','$idade','$email_vendedor','$senha_vendedor','$cpf','$cnpj')";
    $conn -> exec($sql); 

    echo "Boa:";
    header("Location: ../consulta/consulta.php");
    exit();

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>