<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'];
$id_comunidade = $_POST['id_comunidade'];
$mensagem = trim($_POST['mensagem']);
$spoiler = isset($_POST['spoiler']); 

if ($spoiler) {
    $mensagem = "[spoiler]" . $mensagem . "[/spoiler]";
}


$palavroes = ["vai toma no cu", "buceta", 
              "piroca", "caralho", "puta", 
              "foder", "fodase", "merda", 
              "porra", "cuzão", "cuzao", 
              "cu", "xota", "xoxota", 
              "bosta", "boceta", "boceta"];

foreach ($palavroes as $p) {
    $mensagem = preg_replace("/\b" . preg_quote($p, '/') . "\b/i", "###", $mensagem);
}

if (empty(trim($mensagem))) {
    http_response_code(400);
    echo json_encode(["erro" => "Sua mensagem não pode ser enviada apenas com palavras bloqueadas."]);
    exit;
}

// Insere no banco
$stmt = $conn->prepare("
    INSERT INTO mensagens_comunidade (idcomunidades, idusuario, mensagem)
    VALUES (:id_comunidade, :id_usuario, :mensagem)
");
$stmt->execute([
    ':id_comunidade' => $id_comunidade,
    ':id_usuario' => $id_usuario,
    ':mensagem' => $mensagem
]);

echo json_encode(["sucesso" => true]);