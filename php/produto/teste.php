<?php
$ch = curl_init("https://sandbox.melhorenvio.com.br");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resultado = curl_exec($ch);

if ($resultado === false) {
    echo "Erro cURL: " . curl_error($ch);
} else {
    echo "Conectou com sucesso!";
}

curl_close($ch);
