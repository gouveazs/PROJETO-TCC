<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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
// desativa SSL apenas no local
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['erro' => true, 'mensagem' => curl_error($ch)]);
} else {
    echo $response;
}

curl_close($ch);

