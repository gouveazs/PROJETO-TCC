<?php
header('Content-Type: application/json');

if (!isset($_GET['cep'])) {
    echo json_encode(['erro' => true, 'mensagem' => 'CEP não informado']);
    exit;
}

$cep = preg_replace('/[^0-9]/', '', $_GET['cep']);
$url = "https://viacep.com.br/ws/{$cep}/json/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response; // devolve exatamente o JSON do ViaCEP
