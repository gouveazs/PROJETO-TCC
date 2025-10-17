<?php
// SUAS CREDENCIAIS DO APP
$client_id = "20434";
$client_secret = "JHDr1bDQJ5ySyh9lJz9n09MhPMpQbiPMklM3QoEu";
$sandbox = true;

// 1. FUNÇÃO PARA GERAR O ACCESS TOKEN
function gerarTokenMelhorEnvio($client_id, $client_secret, $sandbox = true) {
    $url_token = $sandbox 
        ? "https://sandbox.melhorenvio.com.br/oauth/token"
        : "https://www.melhorenvio.com.br/oauth/token";

    $dados = [
        "grant_type" => "client_credentials",
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "scope" => "shipping-calculate"
    ];

    $ch = curl_init($url_token);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($dados),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: MyApp (seu-email@dominio.com)"
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $resposta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resposta === false) {
        return ["erro" => "Falha ao conectar para gerar token"];
    }

    $dados = json_decode($resposta, true);
    
    if (isset($dados['access_token'])) {
        return [
            "access_token" => $dados['access_token'],
            "token_type" => $dados['token_type'],
            "expires_in" => $dados['expires_in']
        ];
    } else {
        return ["erro" => "Falha ao gerar token: " . ($dados['message'] ?? 'Erro desconhecido')];
    }
}

// 2. FUNÇÃO PARA CALCULAR FRETE (ATUALIZADA)
function calcularFreteMelhorEnvio($cepOrigem, $cepDestino, $peso, $comprimento, $altura, $largura, $token, $sandbox = true) {
    $url_base = $sandbox
        ? "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate"
        : "https://www.melhorenvio.com.br/api/v2/me/shipment/calculate";

    // Preparar os dados no formato JSON esperado pela API
    $dados = [
        "from" => [
            "postal_code" => preg_replace('/\D/', '', $cepOrigem)
        ],
        "to" => [
            "postal_code" => preg_replace('/\D/', '', $cepDestino)
        ],
        "package" => [
            "weight" => floatval($peso),
            "length" => floatval($comprimento),
            "height" => floatval($altura),
            "width" => floatval($largura)
        ],
        "options" => [
            "insurance_value" => 0,
            "receipt" => false,
            "own_hand" => false
        ]
    ];

    $ch = curl_init($url_base);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json",
            "Accept: application/json",
            "User-Agent: MyApp (seu-email@dominio.com)"
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $resposta = curl_exec($ch);
    $erro = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resposta === false) {
        return ["erro" => "Falha na conexão: " . $erro];
    }

    $dadosResposta = json_decode($resposta, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ["erro" => "Resposta inválida da API. HTTP Code: {$httpCode}"];
    }

    if ($httpCode === 401) {
        return ["erro" => "Token inválido ou expirado."];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        $msg = $dadosResposta["message"] ?? $dadosResposta["error"] ?? "Erro HTTP $httpCode";
        return ["erro" => "API retornou erro: $msg"];
    }

    $result = [];
    if (is_array($dadosResposta)) {
        foreach ($dadosResposta as $quote) {
            if (isset($quote['id']) && isset($quote['price'])) {
                $service_code = (string)$quote['id'];
                $result[$service_code] = [
                    "nome" => $quote['name'] ?? "Serviço {$service_code}",
                    "valor" => floatval($quote['price']),
                    "valor_str" => "R$ " . number_format($quote['price'], 2, ",", "."),
                    "prazo" => $quote['delivery_time'] ?? 0,
                    "empresa" => $quote['company']['name'] ?? 'Desconhecida'
                ];
            }
        }
    }

    return empty($result) ? ["erro" => "Nenhuma opção de frete disponível"] : $result;
}

// 3. EXECUÇÃO PRINCIPAL
echo "<h2>Calculando Frete com Melhor Envio</h2>";

// Primeiro: Gerar o Access Token
echo "1. Gerando token de acesso...<br>";
$token_info = gerarTokenMelhorEnvio($client_id, $client_secret, $sandbox);

if (isset($token_info['erro'])) {
    die("Erro ao gerar token: " . $token_info['erro']);
}

$access_token = $token_info['access_token'];
echo "✓ Token gerado com sucesso!<br>";
echo "Token: " . substr($access_token, 0, 20) . "...<br><br>";

// Segundo: Calcular o frete
echo "2. Calculando fretes...<br>";
$resultado = calcularFreteMelhorEnvio(
    "01001000", // CEP origem (São Paulo)
    "22041001", // CEP destino (Rio de Janeiro)
    0.5,        // peso (kg)
    20,         // comprimento (cm)
    10,         // altura (cm)
    15,         // largura (cm)
    $access_token,
    $sandbox
);

// Terceiro: Exibir resultados
if (isset($resultado['erro'])) {
    echo "❌ Erro no cálculo: " . $resultado['erro'];
} else {
    echo "✅ Fretes calculados com sucesso!<br><br>";
    echo "<h3>Opções de Frete Disponíveis:</h3>";
    
    foreach ($resultado as $servico) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>{$servico['nome']}</strong><br>";
        echo "Empresa: {$servico['empresa']}<br>";
        echo "Valor: <strong>{$servico['valor_str']}</strong><br>";
        echo "Prazo: {$servico['prazo']} dias úteis";
        echo "</div>";
    }
}

// 4. INFORMações ADICIONAIS
echo "<br><hr>";
echo "<h4>Informações Técnicas:</h4>";
echo "Client ID: {$client_id}<br>";
echo "Sandbox: " . ($sandbox ? "Sim" : "Não") . "<br>";
echo "Token expira em: " . ($token_info['expires_in'] ?? 'N/A') . " segundos<br>";
?>