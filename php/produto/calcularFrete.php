<?php
function calcularFreteMelhorEnvio(
    $cepVendedor,
    $cepUsuario,
    $peso,
    $comprimento,
    $altura,
    $largura,
    $token,
    $usarSandbox = true,
    $insurance_value = 50.0
) {
    $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate";

    $dados = [
        "from" => ["postal_code" => $cepVendedor],
        "to" => ["postal_code" => $cepUsuario],
        "package" => [
            "height" => $altura,
            "width" => $largura,
            "length" => $comprimento,
            "weight" => $peso
        ],
        "options" => [
            "insurance_value" => $insurance_value,
            "receipt" => false,
            "own_hand" => false
        ],
        "services" => "1,2" // PAC e SEDEX
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Accept: application/json",
            "Content-Type: application/json",
            "User-Agent: EntreLinhas (contato@entrelinhas.com)"
        ],
        CURLOPT_POSTFIELDS => json_encode($dados)
    ]);

    $resposta = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_close($ch);

    if ($httpcode === 200) {
        return json_decode($resposta, true);
    } else {
        return ["erro" => "HTTP $httpcode", "detalhe" => $resposta];
    }
}
