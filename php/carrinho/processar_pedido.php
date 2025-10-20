<?php
session_start();
include '../conexao.php';

// ===============================
// ⚙️ VERIFICA SE O USUÁRIO ESTÁ LOGADO
// ===============================
if (!isset($_SESSION['idusuario'])) {
    header('Location: ../login/login.php');
    exit;
}

$idusuario = $_SESSION['idusuario'];

// ===============================
// ⚙️ VERIFICA SE O CARRINHO EXISTE E TEM ITENS
// ===============================
if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) === 0) {
    die("Carrinho vazio. Adicione produtos antes de confirmar o pedido.");
}

$carrinho = $_SESSION['carrinho'];

// ===============================
// 🧮 FUNÇÃO PARA LIMPAR VALORES
// ===============================
function limparValor($valor) {
    if (is_numeric($valor)) return floatval($valor);
    $valor = str_replace(['R$', ' ', '.'], '', $valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
}

// ===============================
// 💰 CALCULA VALORES GERAIS
// ===============================
$totalProdutos = 0.0;
$totalFrete = 0.0;
$prazos = [];

foreach ($carrinho as $item) {
    $preco = limparValor($item['preco']);
    $freteItem = limparValor($item['frete']['preco'] ?? 0);
    $quantidade = $item['quantidade'] ?? 1;

    $subtotal = $preco * $quantidade;
    $totalProdutos += $subtotal;
    $totalFrete += $freteItem;
    $prazos[] = isset($item['frete']['prazo']) && is_numeric($item['frete']['prazo']) 
                 ? (int)$item['frete']['prazo'] 
                 : 0;
}

$totalGeral = floatval($totalProdutos + $totalFrete);
$prazoMedio = !empty($prazos) ? round(array_sum($prazos) / count($prazos)) : 0;
$servicoFreteGeral = $carrinho[0]['frete']['nome'] ?? 'Frete Padrão';

try {
    // ===============================
    // 🧾 INÍCIO DA TRANSAÇÃO
    // ===============================
    $conn->beginTransaction();

    // ===============================
    // 🛒 INSERE PEDIDO
    // ===============================
    $stmtPedido = $conn->prepare("
        INSERT INTO pedido (valor_total, data_pedido, frete, idusuario, status, servico_frete, prazo_entrega)
        VALUES (?, CURDATE(), ?, ?, 'pendente', ?, ?)
    ");
    $stmtPedido->execute([$totalGeral, $totalFrete, $idusuario, $servicoFreteGeral, $prazoMedio]);
    $idPedido = $conn->lastInsertId();

    // ===============================
    // 📦 INSERE ITENS DO PEDIDO
    // ===============================
    foreach ($carrinho as $item) {

        $quantidadeItem = $item['quantidade'] ?? 1;
        $freteItem = limparValor($item['frete']['preco'] ?? 0);
        $prazoItem = isset($item['frete']['prazo']) && is_numeric($item['frete']['prazo'])
                     ? (int)$item['frete']['prazo']
                     : 0;
        $servicoFreteItem = $item['frete']['nome'] ?? 'Não informado';

        $stmtItem = $conn->prepare("
            INSERT INTO item_pedido (quantidade, idproduto, idpedido, frete_item, servico_frete_item, prazo_item)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmtItem->execute([
            $quantidadeItem,
            $item['id'],
            $idPedido,
            $freteItem,
            $servicoFreteItem,
            $prazoItem
        ]);

        // ===============================
        // 🔔 CRIA NOTIFICAÇÃO PARA O VENDEDOR
        // ===============================
        $stmtVend = $conn->prepare("SELECT idvendedor FROM produto WHERE idproduto = ?");
        $stmtVend->execute([$item['id']]);
        $vendedor = $stmtVend->fetch(PDO::FETCH_ASSOC);

        if ($vendedor) {
            $mensagem = "Um novo pedido foi realizado contendo o produto '{$item['nome']}'.";
            $stmtNotif = $conn->prepare("
                INSERT INTO notificacoes (mensagem, lida, idusuario, idvendedor, tipo)
                VALUES (?, 0, ?, ?, 'compra')
            ");
            $stmtNotif->execute([$mensagem, $idusuario, $vendedor['idvendedor']]);
        }
    }

    // ===============================
    // ✅ CONFIRMA TUDO
    // ===============================
    $conn->commit();

    // ===============================
    // 🧹 LIMPA O CARRINHO E REDIRECIONA
    // ===============================
    unset($_SESSION['carrinho']);
    header("Location: ../sucesso.php"); // página de sucesso
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    die("Erro ao processar pedido: " . $e->getMessage());
}
?>
