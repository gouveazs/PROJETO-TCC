<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include '../../conexao.php';

try {
    $stmt = $conn->query("
        SELECT 
            idproduto,
            idvendedor,
            idcategoria,
            nome,
            numero_paginas,
            editora,
            classificacao_etaria,
            data_publicacao,
            preco,
            quantidade,
            descricao,
            autor,
            isbn,
            dimensoes,
            idioma,
            estado_livro,
            paginas_faltando,
            folhas_amareladas,
            paginas_rasgadas,
            anotacoes,
            lombada_danificada,
            capa_danificada,
            estado_detalhado,
            status
        FROM produto
    ");
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
}
?>
