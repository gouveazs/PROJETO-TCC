<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_comunidade = $_POST['id_comunidade'] ?? null;
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
$spoiler = isset($_POST['spoiler']) && ($_POST['spoiler'] == '1' || $_POST['spoiler'] == 1);

if (!$id_usuario || !$id_comunidade || !ctype_digit((string)$id_comunidade)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos.']);
    exit;
}

if ($spoiler) {
    $mensagem = "[spoiler]" . $mensagem . "[/spoiler]";
}

// filtro básico de palavrões (você pode melhorar com listas externas)
$palavroes = ["vai toma no cu", "buceta", "piroca", "caralho", "puta", "foder", "fodase", "merda", "porra", "cuzão", "cuzao", "cu", "xota", "xoxota", "bosta", "boceta"];

foreach ($palavroes as $p) {
    // usa i para case-insensitive e u para unicode
    $mensagem = preg_replace("/\b" . preg_quote($p, '/') . "\b/iu", "###", $mensagem);
}

if (empty(trim($mensagem))) {
    http_response_code(400);
    echo json_encode(["erro" => "Sua mensagem não pode ser enviada apenas com palavras bloqueadas."]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO mensagens_comunidade (idcomunidades, idusuario, mensagem)
    VALUES (:id_comunidade, :id_usuario, :mensagem)
");
$stmt->execute([
    ':id_comunidade' => $id_comunidade,
    ':id_usuario' => $id_usuario,
    ':mensagem' => $mensagem
]);

$insertId = (int)$conn->lastInsertId();

// opcional: buscar a data exata inserida para devolver ao front
$stmt2 = $conn->prepare("SELECT enviada_em FROM mensagens_comunidade WHERE idmensagens_chat = :id");
$stmt2->execute([':id' => $insertId]);
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
$enviada_em = $row['enviada_em'] ?? null;

echo json_encode(["sucesso" => true, "id" => $insertId, "enviada_em" => $enviada_em]);
