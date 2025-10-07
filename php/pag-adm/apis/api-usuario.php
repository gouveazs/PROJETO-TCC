<?php
header('Content-Type: application/json; charset=utf-8');

include '../../conexao.php';

try {
    $sql = "SELECT idusuario, nome, email, senha, nome_completo, telefone, cpf, cep, estado, cidade, bairro, rua, numero, foto_de_perfil, status FROM usuario";
    $stmt = $conn->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($usuarios) {
        foreach ($usuarios as &$u) {
            if ($u['foto_de_perfil']) {
                $u['foto_de_perfil'] = base64_encode($u['foto_de_perfil']);
            }
        }
    }

    echo json_encode($usuarios ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
}
