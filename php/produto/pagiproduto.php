<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;
$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null;

include '../conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Produto não informado.");
}

$idproduto = (int) $_GET['id'];

try {
    $stmt = $conn->prepare("
        SELECT p.*, i.imagem, v.nome_completo, v.email, v.idvendedor, c.nome AS nome_categoria
        FROM produto p
        LEFT JOIN imagens i ON i.idproduto = p.idproduto
        LEFT JOIN vendedor v ON v.idvendedor = p.idvendedor
        LEFT JOIN categoria c ON c.idcategoria = p.idcategoria
        WHERE p.idproduto = :id
        ORDER BY i.idimagens ASC
    ");
    $stmt->execute([':id' => $idproduto]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$resultados) {
        die('Produto não encontrado.');
    }

    $produto = $resultados[0];
    $imagens = array_filter(array_column($resultados, 'imagem'));
    $nome_produto = $produto['nome'];

    // Produtos relacionados
    $stmt2 = $conn->prepare("SELECT * FROM produto WHERE nome LIKE ? AND idproduto != ?");
    $stmt2->execute(['%' . $nome_produto . '%', $idproduto]);
    $ofertas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$idvendedor = $produto['idvendedor'];

// Buscar o CEP do vendedor
$cep_vendedor = $produto['cep'] ?? null;
if (!$cep_vendedor) {
    $stmtV = $conn->prepare("SELECT cep FROM vendedor WHERE idvendedor = :id");
    $stmtV->execute([':id' => $produto['idvendedor']]);
    $cep_vendedor = $stmtV->fetchColumn();
}

// Buscar o CEP do usuário logado
$cep_usuario = null;
if (isset($_SESSION['idusuario'])) {
    $stmtU = $conn->prepare("SELECT cep FROM usuario WHERE idusuario = :id");
    $stmtU->execute([':id' => $_SESSION['idusuario']]);
    $cep_usuario = $stmtU->fetchColumn();
}

// Atualizar CEP manual
if (!empty($_POST['cep_usuario'])) {
    $_SESSION['cep_usuario'] = preg_replace('/[^0-9]/', '', $_POST['cep_usuario']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$stmtA = $conn->prepare("
    SELECT 
        a.nota,
        a.comentario,
        a.data_avaliacao,
        u.nome_completo AS usuario_nome
    FROM avaliacoes a
    INNER JOIN usuario u ON u.idusuario = a.idusuario
    WHERE a.idvendedor = :idvendedor
    ORDER BY a.data_avaliacao DESC
");
$stmtA->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
$stmtA->execute();
$avaliacoes = $stmtA->fetchAll(PDO::FETCH_ASSOC);

/* Média das notas */
$mediaAvaliacao = 0;
if ($avaliacoes) {
    $soma = 0;
    foreach ($avaliacoes as $av) {
        $soma += $av['nota'];
    }
    $mediaAvaliacao = $soma / count($avaliacoes);
}
?>


<!DOCTYPE html> 
<html lang="pt-BR"> 
<head> 
    <meta charset="UTF-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/> 
    <title><?= htmlspecialchars($produto['nome'] ?? 'Págida de Livro') ?> - Entre Linhas</title> 
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
    <style> 
        :root { 
            --marrom: #5a4224; 
            --verde: #5a6b50; 
            --background: #F4F1EE; 
            --cinza-claro: #f8f9fa;
            --cinza-escuro: #6c757d;
            --laranja: #fd7e14;
            --cinza-texto: #565959;
            --azul-link: #007185;
        } 
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Playfair Display', serif; 
        } 
        body { 
            background-color: var(--background); 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
        } 
        .sidebar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 250px; 
            height: 100vh; 
            background-color: var(--verde); 
            color: #fff; 
            display: flex; 
            flex-direction: column; 
            align-items: flex-start; 
            padding-top: 20px; 
            overflow-y: auto; 
            scrollbar-width: thin; 
            scrollbar-color: #ccc transparent; 
            z-index: 1000;
        } 
        .sidebar::-webkit-scrollbar { 
            width: 6px; 
        } 
        .sidebar::-webkit-scrollbar-thumb { 
            background-color: #ccc; 
            border-radius: 4px; 
        } 
        .sidebar::-webkit-scrollbar-track { 
            background: transparent; 
        } 
        .sidebar .logo { 
            display: flex; 
            align-items: center; 
            justify-content: flex-start; 
            width: 100%; 
            padding: 0 20px; 
            margin-bottom: 20px; 
        } 
        .sidebar .logo img { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-right: 15px; 
        } 
        .sidebar .user-info { 
            display: flex; 
            flex-direction: column; 
            line-height: 1.2; 
        } 
        .sidebar .user-info .nome-usuario { 
            font-weight: bold; 
            font-size: 0.95rem; 
            color: #fff; 
        } 
        .sidebar .user-info .tipo-usuario { 
            font-size: 0.8rem; 
            color: #ddd; 
        } 
        .sidebar .logo p { 
            font-weight: bold; 
        } 
        .sidebar nav { 
            width: 100%; 
            padding: 0 20px; 
        } 
        .sidebar nav h3 { 
            margin-top: 20px; 
            margin-bottom: 10px; 
            font-size: 1rem; 
            color: #ddd; 
        } 
        .sidebar nav ul { 
            list-style: none; 
            padding: 0; 
            margin: 0 0 10px 0; 
            width: 100%; 
        } 
        .sidebar nav ul li { 
            width: 100%; 
            margin-bottom: 10px; 
        } 
        .sidebar nav ul li a { 
            color: #fff; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            padding: 10px; 
            border-radius: 8px; 
            transition: background 0.3s; 
        } 
        .sidebar nav ul li a i { 
            margin-right: 10px; 
            width: 20px;
            text-align: center;
        } 
        .sidebar nav ul li a:hover { 
            background-color: #6f8562; 
        } 
        .topbar { 
            position: fixed; 
            top: 0; 
            left: 250px; 
            right: 0; 
            height: 70px; 
            background-color: var(--marrom); 
            color: #fff; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 30px; 
            z-index: 1001; 
        } 
        .topbar h1 { 
            font-size: 1.5rem; 
        } 
        .search-form {
            display: flex;
            align-items: center;
        }
        .topbar input[type="text"] { 
            padding: 10px 15px; 
            border: none; 
            border-radius: 20px 0 0 20px; 
            width: 250px; 
            font-size: 0.9rem;
        } 
        .topbar input[type="submit"] {
            padding: 10px 15px;
            background: var(--verde);
            color: white;
            border: none;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
        }
        .main { 
            flex: 1; 
            margin-left: 250px; 
            padding: 30px; 
            margin-top: 70px; 
        }
        /* ESTILOS REFORMULADOS PARA A PÁGINA DO PRODUTO */
        .product-header {
            margin-bottom: 25px;
        }
        .product-header h1 {
            font-size: 1.4rem;
            color: var(--cinza-texto);
            margin-bottom: 5px;
            font-weight: 400;
        }
        .product-header h2 {
            font-size: 2.4rem;
            color: var(--marrom);
            margin-bottom: 10px;
            font-weight: 600;
            line-height: 1.2;
        }
        .rating-stock {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        .rating {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rating-stars {
            color: #ffc107;
        }
        .rating-count {
            color: var(--azul-link);
            font-size: 0.9rem;
        }
        .in-stock {
            color: #007600;
            font-size: 1rem;
            font-weight: 500;
        }
        .product-container {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .product-main {
            flex: 1;
            min-width: 300px;
        }
        .product-sidebar {
            width: 320px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #ddd;
        }

        /* CARROSSEL DE IMAGENS */
        .product-image {
            text-align: center;
            margin-bottom: 25px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: relative;
            border: 1px solid #ddd;
        }
        .carousel {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
        }
        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }
        .carousel-item {
            min-width: 100%;
            position: relative;
        }
        .carousel-item img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            border-radius: 6px;
        }
        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: background-color 0.3s;
        }
        .carousel-control:hover {
            background-color: rgba(0,0,0,0.7);
        }
        .carousel-control.prev {
            left: 15px;
        }
        .carousel-control.next {
            right: 15px;
        }
        .carousel-indicators {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 8px;
        }
        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ccc;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .carousel-indicator.active {
            background-color: var(--verde);
        }
        .carousel-thumbnails {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 10px;
        }
        .carousel-thumbnail {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
            border: 2px solid transparent;
        }
        .carousel-thumbnail:hover,
        .carousel-thumbnail.active {
            opacity: 1;
            border-color: var(--verde);
        }
        .carousel-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #ddd;
        }
        .product-info h3 {
            font-size: 1.6rem;
            color: var(--verde);
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 12px;
        }
        .product-info p {
            margin-bottom: 12px;
            font-size: 1.05rem;
            color: #555;
            line-height: 1.5;
        }
        .product-info strong {
            color: var(--marrom);
        }
        .product-conditions {
            margin: 20px 0;
        }
        .condition {
            display: inline-block;
            padding: 6px 16px;
            margin-right: 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .used {
            background-color: #f0f0f0;
            color: #555;
            border: 1px solid #ddd;
        }
        .new {
            background-color: var(--verde);
            color: white;
            border: 1px solid var(--verde);
        }

        /* SEÇÃO DE COMPRA REFORMULADA */
        .buy-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid #ddd;
        }
        .price-container {
            margin: 0 0 15px 0;
        }
        .price-label {
            font-size: 0.9rem;
            color: var(--cinza-texto);
        }
        .product-price {
            font-size: 1.8rem;
            color: var(--marrom);
            font-weight: 600;
            margin: 5px 0;
        }

        /* ESTILOS PARA A SEÇÃO DE FRETE */
        .shipping-info {
            font-size: 0.9rem;
            color: var(--cinza-texto);
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .shipping-info h4 {
            color: var(--marrom);
            margin-bottom: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .shipping-info form {
            display: flex;
            gap: 8px;
            margin: 10px 0;
        }
        .shipping-info input[type="text"] {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            background: white;
        }
        .shipping-info button[type="submit"] {
            background-color: var(--verde);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .shipping-info button[type="submit"]:hover {
            background-color: #4a5a40;
        }
        #frete-options {
            margin-top: 10px;
        }
        #frete-options div {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        #frete-options label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--cinza-texto);
        }
        #frete-options input[type="radio"] {
            margin: 0;
            accent-color: var(--verde);
        }
        #frete-options strong {
            color: var(--marrom);
            font-weight: 600;
        }

        .product-description {
            margin: 15px 0;
            padding: 15px;
            background-color: var(--cinza-claro);
            border-radius: 8px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .product-seller {
            margin: 15px 0;
            padding: 15px;
            background-color: var(--cinza-claro);
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .product-seller strong {
            color: var(--marrom);
        }
        .seller-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
            color: var(--cinza-texto);
            font-size: 0.9rem;
        }

        /* ESTILOS PARA O BOTÃO DE COMPRA */
        .buy-button {
            background-color: var(--laranja);
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 20px 0;
            width: 100%;
            display: block;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
        }
        .buy-button:hover {
            background-color: #e76f00;
            text-decoration: none;
            color: white;
        }
        .buy-button a {
            color: white;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        }
        .buy-button a:hover {
            color: white;
            text-decoration: none;
        }

        /* BOTÃO DE FAVORITOS - MESMO TAMANHO DO COMPRA, CENTRALIZADO E VERDE */
        .btn-favorito {
            background-color: var(--verde);
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 20px 0;
            width: 100%;
            display: block;
            text-align: center;
            font-weight: 600;
        }
        .btn-favorito:hover {
            background-color: #4a5a40;
            color: white;
        }

        .btn-favorito-min {
            background-color: var(--verde);
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 20px 0;
            width: 100%;
            display: block;
            text-align: center;
            font-weight: 600;
        }
        .btn-favorito-min:hover {
            background-color: #4a5a40;
            color: white;
        }

        .secure-transaction {
            text-align: center;
            font-size: 0.85rem;
            color: var(--cinza-texto);
            margin-top: 10px;
        }
        .secure-transaction i {
            color: var(--verde);
            margin-right: 5px;
        }
        .buy-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .buy-option {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            color: var(--cinza-escuro);
        }
        .buy-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--verde);
        }
        .buy-option a {
            color: var(--azul-link);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .buy-option a:hover {
            text-decoration: underline;
            color: #c7511f;
        }

        /* OUTRAS OFERTAS - ESTILO AMAZON */
        .other-offers {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .other-offers h3 {
            font-size: 1.3rem;
            color: var(--verde);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #eee;
        }
        .offer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .offer:last-child {
            border-bottom: none;
        }
        .offer-info {
            flex: 1;
        }
        .offer-condition {
            font-size: 0.9rem;
            color: var(--cinza-escuro);
            margin-bottom: 6px;
        }
        .offer-seller {
            font-size: 0.95rem;
            color: var(--azul-link);
            margin-bottom: 6px;
            font-weight: 500;
        }
        .seller-details {
            font-size: 0.85rem;
            color: var(--cinza-texto);
        }
        .offer-price {
            font-weight: bold;
            color: var(--marrom);
            font-size: 1.2rem;
            margin-top: 5px;
        }
        .offer-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 120px;
        }
        .offer-buy-btn {
            background-color: var(--verde);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        .offer-buy-btn:hover {
            background-color: #4a5a40;
        }
        .more-offers {
            margin-top: 15px;
            text-align: center;
        }
        .more-offers a {
            color: var(--azul-link);
            text-decoration: none;
            font-weight: 500;
        }
        .more-offers a:hover {
            text-decoration: underline;
            color: #c7511f;
        }

        /* ABAS DE CONTEÚDO */
        .product-tabs ul { display: flex; list-style: none; padding: 0; margin:0; background: #f5f5f5; border-bottom: 1px solid #ddd;}
        .product-tabs ul li {
            padding: 13px 25px; cursor: pointer; font-weight: bold; color: #666; border-bottom: 3px solid transparent; transition: 0.15s;
        }
        .product-tabs ul li.active { color: #006633; border-bottom: 3px solid #006633; background: #fff; }
        .tab-content > div { display: none; padding: 25px 15px 10px 5px; }
        .tab-content > div.active { display: block; }

        .footer { 
            margin-left: 250px; 
            background-color: var(--marrom); 
            color: #fff; 
            text-align: center; 
            padding: 20px; 
            margin-top: 40px;
        } 
        @media (max-width: 1200px) { 
            .product-container {
                flex-direction: column;
            }
            .product-sidebar {
                width: 100%;
            }
        } 
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .topbar, .main, .footer {
                margin-left: 220px;
            }
            .topbar {
                flex-direction: column;
                height: auto;
                padding: 15px;
            }
            .topbar h1 {
                margin-bottom: 15px;
            }
            .main {
                padding: 20px;
            }
        }
        @media (max-width: 768px) { 
            .sidebar { 
                width: 200px; 
            } 
            .topbar, .main, .footer { 
                margin-left: 200px; 
            }
            .product-header h1 {
                font-size: 1.3rem;
            }
            .product-header h2 {
                font-size: 2rem;
            }
            .product-tabs li {
                padding: 15px 20px;
                font-size: 1rem;
            }
            .carousel-thumbnails {
                flex-wrap: wrap;
            }
            .offer {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .offer-actions {
                width: 100%;
            }
            .offer-buy-btn {
                width: 100%;
            }
        } 
        @media (max-width: 576px) { 
            .sidebar { 
                display: none; 
            } 
            .topbar, .main, .footer { 
                margin-left: 0; 
            }
            .product-header h1 {
                font-size: 1.2rem;
            }
            .product-header h2 {
                font-size: 1.8rem;
            }
            .product-tabs li {
                padding: 12px 15px;
                font-size: 0.9rem;
            }
            .carousel-thumbnails {
                display: none;
            }
            .product-tabs ul {
                flex-direction: column;
            }
            .product-tabs li {
                border-bottom: 1px solid #eee;
            }
        } 
    </style>
</head> 
<body> 
    <div class="sidebar"> 
        <div class="logo"> 
            <?php if ($foto_de_perfil): ?> 
                <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" alt="Foto de perfil"> 
            <?php else: ?> 
                <img src="../../imgs/usuario.jpg" alt="Foto de Perfil"> 
            <?php endif; ?> 
            <div class="user-info"> 
                <p class="nome-usuario"><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>  
            </div> 
        </div> 
        <nav> 
            <ul class="menu">
                <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
                <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
                <li><a href="../destaques/destaques.php"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
                <li><a href="../favoritos/favoritos.php"><img src="../../imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
                <li><a href="../carrinho/carrinho.php"><img src="../../imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
            </ul>

            <h3>Conta</h3>
            <ul class="account">
                <?php if (!$nome): ?>
                    <li><a href="../login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
                    <li><a href="../cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
                    <li><a href="../cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
                    <li><a href="../login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
                <?php else: ?>
                    <li><a href="../perfil-usuario/ver_perfil.php?aba=Meu Perfil"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
                    <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
                <?php endif; ?>
            </ul> 
        </nav> 
    </div> 
    <div class="topbar"> 
        <h1>Entre Linhas - Sebo Moderno</h1> 
        <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST"> 
            <input type="text" name="nome" placeholder="Pesquisar livros, autores..."> 
            <input type="submit" value="Buscar"> 
        </form> 
    </div> 
    <div class="main"> 
        <!-- Cabeçalho do produto -->
        <div class="product-header">
            <h1><?= htmlspecialchars($produto['autor']) ?></h1>
            <h2><?= htmlspecialchars($produto['nome']) ?></h2>
            
            <div class="rating-stock">
                <div class="rating">
                    <div class="rating-stars">
                        <?php
                            if ($mediaAvaliacao) {
                                $fullStars = floor($mediaAvaliacao);
                                $halfStar = ($mediaAvaliacao - $fullStars >= 0.5);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $fullStars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($halfStar && $i == $fullStars + 1) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                            } else {
                                // Se não há avaliações
                                for ($i = 1; $i <= 5; $i++) {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                        ?>
                    </div>
                    <span class="rating-count">
                        <?= number_format($mediaAvaliacao, 1, ',', '') ?> (<?= count($avaliacoes) ?> avaliação<?= count($avaliacoes) != 1 ? 's' : '' ?>)
                    </span>
                </div>
                <div class="in-stock">
                    <i class="fas fa-check-circle"></i> Em estoque
                </div>
            </div>
        </div>
        
        <div class="product-container">
            <!-- Coluna principal -->
            <div class="product-main">
                <!-- Carrossel de imagens do produto -->
                <div class="product-image">
                <div class="carousel">
                    <div class="carousel-inner">
                        <?php if (!empty($imagens)): ?>
                            <?php foreach ($imagens as $index => $img): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-item active">
                                <img src="imgs/usuario.jpg" alt="Sem imagem">
                            </div>
                        <?php endif; ?>
                    </div>

                    <button class="carousel-control prev" onclick="moveSlide(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control next" onclick="moveSlide(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="carousel-indicators">
                        <?php if (!empty($imagens)): ?>
                            <?php foreach ($imagens as $index => $img): ?>
                                <span class="carousel-indicator <?= $index === 0 ? 'active' : '' ?>" 
                                    onclick="goToSlide(<?= $index ?>)"></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="carousel-thumbnails">
                    <?php if (!empty($imagens)): ?>
                        <?php foreach ($imagens as $index => $img): ?>
                            <div class="carousel-thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)">
                                <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="Miniatura <?= $index + 1 ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
                
                <!-- Informações do produto -->
                <div class="product-info">
                    <h3>Estado do livro:</h3>
                    <p><strong>Páginas faltando?</strong> <?= htmlspecialchars($produto['paginas_faltando']) ?> </p>
                    <p><strong>Páginas rasgadas?</strong> <?= htmlspecialchars($produto['paginas_rasgadas']) ?></p>
                    <p><strong>Páginas amareladas?</strong>  <?= htmlspecialchars($produto['folhas_amareladas']) ?></p>
                    <p><strong>Possui anotações/rabiscos?</strong> <?= htmlspecialchars($produto['anotacoes']) ?></p>
                    <p><strong>Lombada danificada?</strong> <?= htmlspecialchars($produto['lombada_danificada']) ?></p>
                    <p><strong>Capa danificada?</strong> <?= htmlspecialchars($produto['capa_danificada']) ?></p>
                    <p><strong>Dimensões:</strong> <?= htmlspecialchars($produto['dimensoes']) ?></p>
                </div>
                
                <!-- Abas de conteúdo -->
                <div class="product-tabs">
                    <ul id="product-tabs-list">
                        <li class="active">Sinopse</li>
                        <li>Detalhes</li>
                        <li>Avaliações</li>
                    </ul>
                    <div class="tab-content" id="product-tab-content">
                        <div class="active">
                            <p><?= htmlspecialchars($produto['descricao']) ?></p>
                        </div>
                        <div>
                            <h4>Informações do produto</h4>
                            <ul>
                                <li><strong>Autor:</strong>   <?= htmlspecialchars($produto['autor']) ?> </li>
                                <li><strong>Número de páginas:</strong> <?= htmlspecialchars($produto['numero_paginas']) ?></li>
                                <li><strong>Editora:</strong> <?= htmlspecialchars($produto['editora']) ?></li>
                                <li><strong>Idioma:</strong>  <?= htmlspecialchars($produto['idioma']) ?></li>
                                <li><strong>ISBN:</strong> <?= htmlspecialchars($produto['isbn']) ?></li>
                                <li><strong>Classificação etária:</strong> <?= htmlspecialchars($produto['classificacao_etaria']) ?></li>
                            </ul>
                        </div>
                        <div>
                            <h4>Avaliações dos clientes</h4>
                            <p><strong>4.7 de 5 estrelas</strong> (8.443 avaliações de clientes)</p>
                            <?php if ($avaliacoes): ?>
                                <?php foreach ($avaliacoes as $av): ?>
                                    <div class="avaliacao-item">
                                        <strong><?= htmlspecialchars($av['usuario_nome']) ?></strong><br>
                                        Nota: <?= str_repeat("⭐", $av['nota']) ?><br>
                                        <em><?= htmlspecialchars($av['comentario']) ?></em><br>
                                        <small><?= date('d/m/Y H:i', strtotime($av['data_avaliacao'])) ?></small>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Sem avaliações ainda.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Barra lateral de compra -->
            <div class="product-sidebar">
                <!-- Seção de compra -->
                <div class="buy-section">
                    <div class="price-container">
                        <div class="price-label">Preço:</div>
                        <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                    
                    </div>

                    <div class="shipping-info">
                        <?php
                        include 'calcularFrete.php'; 

                        // Se o usuário estiver logado, busca o CEP do banco ou sessão
                        $cepVendedor = $cep_vendedor ?? null;
                        $cepUsuario = $_SESSION['cep_usuario'] ?? ($cep_usuario ?? null);

                        // Dados do produto
                        $peso = ($produto['peso'] ?? 1000) / 1000; // kg
                        $altura = $produto['altura'] ?? 10;
                        $largura = $produto['largura'] ?? 15;
                        $comprimento = $produto['comprimento'] ?? 20;

                        // Token Melhor Envio
                        $tokenMelhorEnvio = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NTYiLCJqdGkiOiI1Yzk5OGE3MTAzYmQ1YmFmN2E4M2E3ZTZlMDNmZTljMjBlMzM2NzFmM2UxNTg4ZjJjZDFmMjBkYTc2ZGZhZTgzZGIyMzMzYTU2ZmE2YWE2ZiIsImlhdCI6MTc2MDczNDk3OS41MDA4MzUsIm5iZiI6MTc2MDczNDk3OS41MDA4MzgsImV4cCI6MTc5MjI3MDk3OS40OTE5MDksInN1YiI6ImEwMjM0ZGE4LTM5YmUtNGEzNS04MmRlLTU1N2ViZWYwNjFlOSIsInNjb3BlcyI6WyJzaGlwcGluZy1jYWxjdWxhdGUiXX0.pz7RG3El6bSCnyeB2ItjAuW80erggbMSL5RVcOfvZz3u93dDpS4inndljPiCoTNzJ0u2pFxPFPm8gICClcb4Yz9pVn2jpPXJQqQw5Z_xFkHri_g6FF-3nFfw57FJ7g3HyEKna0TN3auLpVfO1km6QOlHftsITGe8k3x2KjaV2XgjbvDWvIPBubc1lF0lRsPdKihvfigW2KpihOLVbOSNkVGG3omSOgb5E3FPwykyDHIeV9rdoefzmiKVV5W2yc9C2Y5mRiPeyJIcL2-OnJLjXtDtrT2UY4-1mLIEWqpL-2Di1NFeKIcaqbIsZ6YpwZsBQeGh9ySti0Lgn-dpyuQBlNc2m0_MHRN2BA-Hsr_owmXykrZDsjQuIyt-ijtNluzfhvahYaoJ7GjagurOowoLMeecEoQNNhuRIQ_IarQ0EsDYTh5hl-gFT7VtlNmDv6Fv7Ut8CfEkAcus2v8PEt9shKxONQxd9hZVq9QR0lFZVRNYvwmd_9VUn_aAYhJ9LJnlwcSJC4XV67nKXg5_qe28bouktyrZ4mrjCNdcjWmKBHHAj4ShSQmydmJ9_iNg6Ud8OMDc8dVwWwWwdfFbVj4wMJIuO6lA9T6TczMlORfz-mjqZAdqZ3422JuEhnZWwsPa7V4xZgTFGVezIPAo0rADfPg6ycJWsocl3kz0cfuKjIE";
                        $usarSandbox = true;

                        // Se o formulário de CEP foi enviado manualmente
                        if (!empty($_POST['cep_usuario'])) {
                            $_SESSION['cep_usuario'] = preg_replace('/[^0-9]/', '', $_POST['cep_usuario']);
                            header("Refresh:0");
                            exit;
                        }

                        $freteSelecionado = null;

                        if (!empty($cepUsuario)) {
                            $fretes = calcularFreteMelhorEnvio(
                                $cepVendedor,
                                $cepUsuario,
                                $peso,
                                $comprimento,
                                $altura,
                                $largura,
                                $tokenMelhorEnvio,
                                $usarSandbox
                            );

                            if (isset($fretes['erro'])) {
                                echo "Erro ao calcular frete: {$fretes['erro']}<br>";
                                echo "Detalhe técnico: {$fretes['detalhe']}";
                            } elseif (!empty($fretes)) {
                                echo '<h4>Escolha o tipo de frete:</h4>';
                                echo '<div id="frete-options">';
                                
                                // Identificar o frete mais barato
                                $freteMaisBarato = null;
                                foreach ($fretes as $f) {
                                    $preco = floatval($f['packages'][0]['price'] ?? 999999);
                                    if ($freteMaisBarato === null || $preco < $freteMaisBarato['preco']) {
                                        $freteMaisBarato = [
                                            'nome' => $f['name'],
                                            'preco' => $preco,
                                            'prazo' => $f['delivery_time'] ?? '-',
                                            'logo' => $f['company']['picture'] ?? ''
                                        ];
                                    }
                                }

                                // Exibir as opções e marcar o mais barato
                                foreach ($fretes as $f) {
                                    $preco = $f['packages'][0]['price'] ?? '-';
                                    $prazo = $f['delivery_time'] ?? '-';
                                    $logo = $f['company']['picture'] ?? '';
                                    $nome = $f['name'];

                                    $value = base64_encode(json_encode([
                                        'nome' => $nome,
                                        'preco' => $preco,
                                        'prazo' => $prazo
                                    ]));

                                    $checked = ($freteMaisBarato && $nome === $freteMaisBarato['nome']) ? "checked" : "";

                                    if ($checked) {
                                        $freteSelecionado = [
                                            'nome' => $nome,
                                            'preco' => $preco,
                                            'prazo' => $prazo
                                        ];
                                    }

                                    echo '<div style="margin-bottom:5px;">';
                                    echo "<label>";
                                    echo "<input type='radio' name='frete' value='$value' $checked> ";
                                    if ($logo) {
                                        echo "<img src='$logo' width='40' style='vertical-align:middle;'> ";
                                    }
                                    echo "<strong>$nome</strong> — R$ $preco — Prazo: $prazo dias úteis";
                                    echo "</label>";
                                    echo '</div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p>Nenhum serviço de frete retornado.</p>';
                            }
                        } else {
                            echo '<h4>Calcular frete</h4>';
                            echo '<p>Informe seu CEP para calcular o frete:</p>';
                            echo '<form method="post" action="">';
                            echo '  <input type="text" name="cep_usuario" placeholder="Digite seu CEP" maxlength="9" required>';
                            echo '  <button type="submit">Calcular Frete</button>';
                            echo '</form>';
                        }
                        ?>
                    </div>

                    <div class="product-description">
                        <p><?= htmlspecialchars($produto['estado_livro']) ?></p>
                    </div>

                     <div class="product-description">
                        <p>Categoria: <?= htmlspecialchars($produto['nome_categoria']) ?></p>
                    </div>
                    
                    <div class="product-seller">
                        <p>Vendido por: <strong><?= htmlspecialchars($produto['nome_completo']) ?></strong></p>
                        <div class="seller-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            4.7 (1.284 avaliações)
                        </div>
                    </div>
                    
                    <?php
                        $fretePadrao = null;

                        if (!empty($fretes) && is_array($fretes)) {
                            $primeiroFrete = reset($fretes);
                        
                            if (is_array($primeiroFrete) && isset($primeiroFrete['name'])) {
                                $fretePadrao = base64_encode(json_encode([
                                    'nome' => $primeiroFrete['name'] ?? '',
                                    'preco' => $primeiroFrete['packages'][0]['price'] ?? 0,
                                    'prazo' => $primeiroFrete['delivery_time'] ?? '-'
                                ]));
                            } else {
                                // debug opcional:
                                 echo '<pre>'; var_dump($primeiroFrete); echo '</pre>';
                            }
                        }                        
                    ?>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const freteRadios = document.querySelectorAll('input[name="frete"]');
                            const buyButton = document.querySelector('#buy-button a');

                            freteRadios.forEach(radio => {
                                radio.addEventListener('change', function() {
                                    const selectedFrete = encodeURIComponent(this.value);
                                    // Pega a URL base (sem frete)
                                    let baseUrl = buyButton.href.split('&frete=')[0];
                                    // Atualiza a URL com o frete selecionado
                                    buyButton.href = baseUrl + '&frete=' + selectedFrete;
                                });

                                // Caso algum rádio já venha marcado (o mais barato)
                                if (radio.checked) {
                                    const selectedFrete = encodeURIComponent(radio.value);
                                    let baseUrl = buyButton.href.split('&frete=')[0];
                                    buyButton.href = baseUrl + '&frete=' + selectedFrete;
                                }
                            });
                        });
                    </script>

                    <button class="buy-button" id="buy-button">
                        <a href="../carrinho/carrinho.php?id=<?= $produto['idproduto'] ?>&nome=<?= urlencode($produto['nome']) ?>&preco=<?= $produto['preco'] ?>&frete=<?= $fretePadrao ?>" class="card-link">
                            COMPRAR
                        </a>
                    </button>
                    
                    <div class="secure-transaction">
                        <i class="fas fa-lock"></i> Transação segura
                    </div>
                    
                    <div class="buy-options">
                        <div class="buy-option">
                            <a href="../chat/chat.php?idvendedor=<?= $produto['idvendedor'] ?>&remetente_tipo=usuario">
                                <i class="fas fa-comments"></i> Falar com vendedor
                            </a>
                        </div>
                        <div class="buy-option">
                            <?php
                                if (!isset($_SESSION['idusuario'])) {
                                    echo '<p><a href="../login/login.php">Faça login para adicionar aos favoritos</a></p>';
                                } else {
                                    if (isset($_GET['id'])) {
                                        $idproduto = (int)$_GET['id'];
                                        $idusuario = (int)$_SESSION['idusuario'];

                                        // verifica se já existe nos favoritos
                                        include '../conexao.php'; // seu arquivo de conexão PDO

                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM favoritos WHERE idusuario = :idusuario AND idproduto = :idproduto");
                                        $stmt->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
                                        $stmt->bindValue(':idproduto', $idproduto, PDO::PARAM_INT);
                                        $stmt->execute();
                                        $jaFavorito = $stmt->fetchColumn();

                                        if ($jaFavorito) {
                                            echo '<p style="color: green;">Este produto já está nos seus favoritos </p>';
                                        } else {
                                            echo '
                                            <form method="post" action="../insercao/insercaoFavoritos.php">
                                                <input type="hidden" name="idproduto" value="'.$idproduto.'">
                                                <button type="submit" class="btn-favorito-min">Adicionar aos Favoritos ❤</button>
                                            </form>';
                                        }

                                        // mensagem de feedback
                                        if (isset($_GET['favorito']) && $_GET['favorito'] == 'sucesso') {
                                            echo '<p style="color: green; margin-top: 10px;">Produto adicionado aos favoritos!</p>';
                                        } elseif (isset($_GET['favorito']) && $_GET['favorito'] == 'erro') {
                                            echo '<p style="color: red; margin-top: 10px;">Erro ao adicionar aos favoritos. Tente novamente.</p>';
                                        }

                                    } else {
                                        echo "<p>Produto não encontrado.</p>";
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Outras ofertas -->
                <div class="other-offers">
                    <h3>Outras ofertas</h3>
                    <?php if (count($ofertas) > 0): ?>
                        <?php foreach ($ofertas as $oferta): ?>
                            <div class="offer">
                                <div class="offer-info">
                                    <div class="offer-condition"><?= htmlspecialchars($oferta['nome']) ?></div>
                                    <div class="offer-seller"><?= htmlspecialchars($oferta['estado_livro']) ?></div>
                                    <div class="offer-price">R$ <?= number_format($oferta['preco'], 2, ',', '.') ?></div>
                                </div>
                                <div class="offer-actions">
                                     <a href="pagiproduto.php?id=<?= urlencode($oferta['idproduto']) ?>">
                                        <button class="offer-buy-btn">Comprar</button>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($ofertas) > 3): ?>
                            <div class="more-offers">
                                <a href="#">Ver as outras <?= count($ofertas) - 3 ?> ofertas deste produto</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Não há outras ofertas para este produto.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> 
    <div class="footer"> 
        &copy; 2025 Entre Linhas - Todos os direitos reservados. 
    </div> 
    
    <script>
        
        // Funções para o carrossel de imagens
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;
        const carouselInner = document.querySelector('.carousel-inner');
        const indicators = document.querySelectorAll('.carousel-indicator');
        const thumbnails = document.querySelectorAll('.carousel-thumbnail');
        
        function updateCarousel() {
            carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            // Atualizar indicadores
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentSlide);
            });
            
            // Atualizar miniaturas
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.classList.toggle('active', index === currentSlide);
            });
        }
        
        function moveSlide(direction) {
            currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
            updateCarousel();
        }
        
        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }
        
        // Auto-play do carrossel (opcional)
        let carouselInterval = setInterval(() => {
            moveSlide(1);
        }, 5000);
        
        // Pausar auto-play ao interagir com o carrossel
        const carousel = document.querySelector('.carousel');
        carousel.addEventListener('mouseenter', () => {
            clearInterval(carouselInterval);
        });
        
        carousel.addEventListener('mouseleave', () => {
            carouselInterval = setInterval(() => {
                moveSlide(1);
            }, 5000);
        });
        
       document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#product-tabs-list li');
            const contents = document.querySelectorAll('#product-tab-content > div');
            tabs.forEach((tab, idx) => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    tab.classList.add('active');
                    if (contents[idx]) contents[idx].classList.add('active');
                });
            });
        });

    </script>
</body> 
</html>