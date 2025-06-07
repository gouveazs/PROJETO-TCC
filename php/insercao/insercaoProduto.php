<?php
include '../conexaoVendedor.php';

$nome = $_POST['nome'];
$num_paginas = $_POST['numero_paginas'];
$editora = $_POST['editora'];
$autor = $_POST['autor'];
$classificacao = $_POST['classificacao_idade'];
$data_publicacao = $_POST['data_publicacao'];
$preco = $_POST['preco'];
$quantidade = $_POST['quantidade'];
$descricao = $_POST['descricao'];

// Verifica se a imagem foi enviada corretamente
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
} else {
    die("Erro ao enviar a imagem.");
}

try {
    $sql = "INSERT INTO produto 
        (nome, numero_paginas, editora, autor, classificacao_idade, data_publicacao, preco, quantidade, descricao, imagem, idvendedor) 
        VALUES 
        (:nome, :num_paginas, :editora, :autor, :classificacao, :data_publicacao, :preco, :quantidade, :descricao, :imagem, 3)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':num_paginas', $num_paginas);
    $stmt->bindParam(':editora', $editora);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':classificacao', $classificacao);
    $stmt->bindParam(':data_publicacao', $data_publicacao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);

    $stmt->execute();

    header("Location: ../consulta/consulta.php");
    exit();

} catch(PDOException $e) {
    echo "Erro ao inserir: " . $e->getMessage();
}

$conn = null;
?>
