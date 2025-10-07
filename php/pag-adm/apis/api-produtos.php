<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include '../../conexao.php';

try {
    $stmt = $conn->query("
        SELECT 
            p.*,
            i.imagem
        FROM produto p
        LEFT JOIN imagens i 
            ON i.idproduto = p.idproduto
            AND i.idimagens = (
                SELECT MIN(idimagens) 
                FROM imagens 
                WHERE idproduto = p.idproduto
            )
    ");

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Converte imagem para base64
    foreach ($produtos as &$p) {
        if (!empty($p['imagem'])) {
            $p['imagem'] = base64_encode($p['imagem']);
        }
    }

    echo json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
}
