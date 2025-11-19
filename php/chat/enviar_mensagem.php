<?php
include '../conexao.php';

$idconversa = $_POST['idconversa'] ?? null;
$idusuario = $_POST['idusuario'] ?? null;
$idvendedor = $_POST['idvendedor'] ?? null;
$mensagem = trim($_POST['mensagem'] ?? '');
$remetente_tipo = $_POST['remetente_tipo'] ?? 'usuario';

if (!$idconversa || empty($mensagem)) {
    exit('Mensagem inválida.');
}

$palavroes = ["vai toma no cu", "buceta", "piroca", "caralho", "puta", "foder", "fodase", "merda", "porra", "cuzão", "cuzao", "cu", "xota", "xoxota", "bosta", "boceta"];
foreach ($palavroes as $p) {
    $mensagem = preg_replace("/\b" . preg_quote($p, '/') . "\b/iu", "###", $mensagem);
}

$remetente_id = ($remetente_tipo === 'usuario') ? $idusuario : $idvendedor;

$stmt = $conn->prepare("
    INSERT INTO mensagens_chat (idconversa, remetente_tipo, remetente_id, conteudo, data_envio, lida)
    VALUES (?, ?, ?, ?, NOW(), 0)
");

if ($stmt->execute([$idconversa, $remetente_tipo, $remetente_id, $mensagem])) {
    echo 'ok';
} else {
    echo 'Erro ao enviar mensagem.';
}
?>
