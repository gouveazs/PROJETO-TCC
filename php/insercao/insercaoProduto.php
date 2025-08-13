<?php
session_start();
include '../conexao.php';

// Dados do formulário
$nome = $_POST['nome'];
$num_paginas = $_POST['numero_paginas'];
$editora = $_POST['editora'];
$autor = $_POST['autor'];
$classificacao = $_POST['classificacao_idade'];
$data_publicacao = $_POST['data_publicacao'];
$preco = $_POST['preco'];
$quantidade = $_POST['quantidade'];
$descricao = $_POST['descricao'];

$idvendedor = isset($_SESSION['id_vendedor']) ? $_SESSION['id_vendedor'] : null;
if (!$idvendedor) {
    die("Vendedor não está logado.");
}

// 1. Insere o produto (sem imagem)
try {
    $sql = "INSERT INTO produto 
        (nome, numero_paginas, editora, autor, classificacao_etaria, data_publicacao, preco, quantidade, descricao, idvendedor) 
        VALUES 
        (:nome, :num_paginas, :editora, :autor, :classificacao, :data_publicacao, :preco, :quantidade, :descricao, :idvendedor)";
    
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
    $stmt->bindParam(':idvendedor', $idvendedor, PDO::PARAM_INT);

    $stmt->execute();
    // Pega o id do produto inserido
    $idproduto = $conn->lastInsertId();

    // 2. Salva cada imagem enviada
    if (!empty($_FILES['imagens']['name'][0])) {
        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagens']['error'][$key] === 0) {
                $imagem = file_get_contents($tmp_name);
                $sql_img = "INSERT INTO imagens (imagem, idproduto) VALUES (:imagem, :idproduto)";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
                $stmt_img->bindParam(':idproduto', $idproduto, PDO::PARAM_INT);
                $stmt_img->execute();
            }
        }
    } else {
        die("Pelo menos uma imagem deve ser enviada.");
    }

    header("Location: ../consulta/consulta.php");
    exit();

} catch(PDOException $e) {
    echo "Erro ao inserir: " . $e->getMessage();
}

$conn = null;
?>