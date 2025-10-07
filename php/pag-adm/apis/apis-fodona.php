<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include '../../conexao.php';

try {
    $stmt = $conn->query("SELECT idusuario, nome, email, telefone, status FROM usuario");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT idvendedor, nome_completo, email, reputacao, status FROM vendedor");
    $vendedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT idcomunidades, nome, descricao, criada_em, status FROM comunidades");
    $comunidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dados = [
        "usuarios" => $usuarios,
        "vendedores" => $vendedores,
        "comunidades" => $comunidades
    ];

    echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
}
