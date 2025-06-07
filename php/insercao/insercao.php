<?php
include '../conexao.php';

//dados usuario
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

try {
    $sql = "INSERT INTO cadastro_usuario (nome, email, senha) VALUES ('$nome','$email','$senha')";
    $conn -> exec($sql);
    echo "Boa:";
    header("Location: ../consulta/consulta.php");
    exit();

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>