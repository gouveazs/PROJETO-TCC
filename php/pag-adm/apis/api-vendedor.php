<?php
header('Content-Type: application/json; charset=utf-8');

include '../../conexao.php';

try {
    $sql = "SELECT idvendedor, nome_completo, email, senha, data_nascimento, telefone, cpf, cnpj, 
                   foto_de_perfil, reputacao, status, cep, estado, cidade, bairro, rua, numero 
            FROM vendedor";

    $stmt = $conn->query($sql);
    $vendedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se houver fotos, converte para base64
    if ($vendedores) {
        foreach ($vendedores as &$v) {
            if ($v['foto_de_perfil']) {
                $v['foto_de_perfil'] = base64_encode($v['foto_de_perfil']);
            }
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($vendedores ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
}
