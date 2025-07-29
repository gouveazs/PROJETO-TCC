<?php

$cep = "13737-655";
$url = "https://viacep.com.br/ws/{$cep}/json";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);
if (isset($data['erro'])) {
    echo "CEP não encontrado.";
} else {
    echo $data['Bairro'];
}

?>