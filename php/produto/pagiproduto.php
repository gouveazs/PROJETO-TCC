<?php 
session_start(); 
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null; 
$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null; 
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null; 

//produtos 
include '../conexao.php'; 
$stmt = $conn->prepare("SELECT * FROM produto"); 
$stmt->execute(); 
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?> 

<!DOCTYPE html> 
<html lang="pt-BR"> 
<head> 
    <meta charset="UTF-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/> 
    <title>Entre Linhas - Livraria Moderna</title> 
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        :root { 
            --marrom: #5a4224; 
            --verde: #5a6b50; 
            --background: #F4F1EE; 
            --cinza-claro: #f8f9fa;
            --cinza-escuro: #6c757d;
            --laranja: #fd7e14;
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
        /* NOVOS ESTILOS PARA A PÁGINA DO PRODUTO */
        .product-header {
            margin-bottom: 25px;
            text-align: center;
        }
        .product-header h1 {
            font-size: 1.8rem;
            color: var(--marrom);
            margin-bottom: 5px;
            font-weight: 600;
        }
        .product-header h2 {
            font-size: 2.8rem;
            color: var(--verde);
            margin-bottom: 15px;
            font-weight: 700;
        }
        .product-container {
            display: flex;
            gap: 40px;
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
        }
        .product-image {
            text-align: center;
            margin-bottom: 25px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .product-image img {
            max-width: 100%;
            height: auto;
            max-height: 400px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .product-info {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
        .buy-section {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .product-price {
            margin: 0 0 20px 0;
            padding: 15px;
            background-color: var(--cinza-claro);
            border-radius: 8px;
            text-align: center;
        }
        .product-price strong {
            font-size: 2rem;
            color: var(--marrom);
        }
        .product-description {
            margin: 15px 0;
            padding: 15px;
            background-color: var(--cinza-claro);
            border-radius: 8px;
            font-size: 1rem;
            line-height: 1.5;
        }
        .product-seller {
            margin: 15px 0;
            padding: 15px;
            background-color: var(--cinza-claro);
            border-radius: 8px;
            font-size: 1rem;
        }
        .product-seller strong {
            color: var(--marrom);
        }
        .buy-button {
            background-color: var(--laranja);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 20px 0;
            width: 100%;
            display: block;
            text-align: center;
            font-weight: 600;
        }
        .buy-button:hover {
            background-color: #e76f00;
        }
        .buy-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
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
            color: var(--marrom);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .buy-option a:hover {
            text-decoration: underline;
        }
        .other-offers {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
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
            padding: 18px 0;
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
            color: var(--marrom);
            margin-bottom: 6px;
            font-weight: 500;
        }
        .offer-price {
            font-weight: bold;
            color: var(--marrom);
            font-size: 1.2rem;
        }
        .offer-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
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
            color: var(--marrom);
            text-decoration: none;
            font-weight: 500;
        }
        .more-offers a:hover {
            text-decoration: underline;
        }
        .product-tabs {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 25px;
        }
        .product-tabs ul {
            display: flex;
            list-style: none;
            border-bottom: 2px solid #eee;
            padding: 0;
            margin: 0;
        }
        .product-tabs li {
            padding: 18px 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            color: #777;
            transition: all 0.3s;
        }
        .product-tabs li.active {
            color: var(--marrom);
            border-bottom: 3px solid var(--marrom);
        }
        .product-tabs li:hover {
            color: var(--marrom);
        }
        .tab-content {
            padding: 25px;
            min-height: 200px;
        }
        .tab-content div {
            display: none;
            line-height: 1.6;
        }
        .tab-content div.active {
            display: block;
        }
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
                font-size: 1.6rem;
            }
            .product-header h2 {
                font-size: 2.2rem;
            }
            .product-tabs li {
                padding: 15px 20px;
                font-size: 1rem;
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
                font-size: 1.4rem;
            }
            .product-header h2 {
                font-size: 2rem;
            }
            .product-tabs li {
                padding: 12px 15px;
                font-size: 0.9rem;
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
                <p class="tipo-usuario"><?= $tipo ? htmlspecialchars($tipo) : 'Usuário'; ?></p> 
            </div> 
        </div> 
        <nav> 
            <ul class="menu"> 
                <li><a href="../../index.php"><i class="fas fa-home"></i> Início</a></li> 
                <li><a href="../comunidades/comunidade.php"><i class="fas fa-users"></i> Comunidades</a></li> 
                <li><a href="#"><i class="fas fa-star"></i> Destaques</a></li> 
                <li><a href="#"><i class="fas fa-heart"></i> Favoritos</a></li> 
                <li><a href="#"><i class="fas fa-shopping-cart"></i> Carrinho</a></li> 
            </ul> 
            <h3>Conta</h3> 
            <ul class="account"> 
                <?php if (!$nome): ?> 
                    <li><a href="../login/login.php"><i class="fas fa-sign-in-alt"></i> Entrar na conta</a></li> 
                    <li><a href="../cadastro/cadastroUsuario.php"><i class="fas fa-user-plus"></i> Criar conta</a></li> 
                    <li><a href="../cadastro/cadastroVendedor.php"><i class="fas fa-store"></i> Quero vender</a></li> 
                    <li><a href="../loginVendedor.php"><i class="fas fa-tachometer-alt"></i> Painel do Livreiro</a></li> 
                <?php else: ?> 
                    <li><a href="../perfil/ver_perfil.php"><i class="fas fa-user"></i> Ver perfil</a></li> 
                <?php endif; ?> 
                <?php if ($nome === 'adm'): ?> 
                    <li><a href="../consulta/consulta.php"><i class="fas fa-search"></i> Consulta</a></li> 
                    <li><a href="../consultaFiltro/busca.php"><i class="fas fa-search"></i> Consulta por Nome</a></li> 
                    <li><a href="../cadastro/cadastroProduto.php"><i class="fas fa-plus-circle"></i> Cadastrar Produto</a></li> 
                <?php endif; ?> 
                <?php if ($tipo === 'Vendedor'): ?> 
                    <li><a href="../cadastro/cadastroProduto.php"><i class="fas fa-plus-circle"></i> Cadastrar Produto</a></li> 
                <?php endif; ?> 
                <?php if ($nome): ?> 
                    <li><a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li> 
                <?php endif; ?> 
            </ul> 
        </nav> 
    </div> 
    <div class="topbar"> 
        <h1>Entre Linhas - Livraria Moderna</h1> 
        <form class="search-form" action="php/consultaFiltro/consultaFiltro.php" method="POST"> 
            <input type="text" name="nome" placeholder="Pesquisar livros, autores..."> 
            <input type="submit" value="Buscar"> 
        </form> 
    </div> 
    <div class="main"> 
        <!-- Cabeçalho do produto -->
        <div class="product-header">
            <h1>GEORGE ORWELL</h1>
            <h2>1984</h2>
        </div>
        
        <div class="product-container">
            <!-- Coluna principal -->
            <div class="product-main">
                <!-- Imagem do produto -->
                <div class="product-image">
                    <img src="../../imgs/1984.jpg" alt="Capa do livro 1984">
                </div>
                
                <!-- Informações do produto -->
                <div class="product-info">
                    <h3>1984</h3>
                    <p><strong>Autor:</strong> George Orwell</p>
                    <p><strong>Ano:</strong> 1949</p>
                    <p><strong>ISBN:</strong> 9788533914849</p>
                    
                    <div class="product-conditions">
                        <span class="condition used">Usado</span>
                        <span class="condition new">Novo</span>
                    </div>
                </div>
                
                <!-- Abas de conteúdo -->
                <div class="product-tabs">
                    <ul>
                        <li class="active" onclick="changeTab(0)">Descrição</li>
                        <li onclick="changeTab(1)">Informações Adicionais</li>
                        <li onclick="changeTab(2)">Avaliações</li>
                    </ul>
                    <div class="tab-content">
                        <div class="active">
                            <p>Uma distopia clássica que retrata um futuro sombrio sob um regime totalitário. O romance é centrado na vida de Winston Smith, um funcionário do Ministério da Verdade que se rebela contra a opressão do Partido e do Grande Irmão.</p>
                            <p>O livro explora temas como vigilância governamental, manipulação da verdade, controle de pensamento e a luta pela liberdade individual.</p>
                        </div>
                        <div>
                            <p><strong>Editora:</strong> Companhia das Letras</p>
                            <p><strong>Edição:</strong> 1ª edição</p>
                            <p><strong>Número de páginas:</strong> 416</p>
                            <p><strong>Idioma:</strong> Português</p>
                            <p><strong>Dimensões:</strong> 23 x 16 x 2 cm</p>
                        </div>
                        <div>
                            <p><strong>Avaliação média:</strong> 4.8/5 (1.234 avaliações)</p>
                            <div style="margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <p><strong>João Silva:</strong> "Uma obra-prima da literatura distópica. Leitura obrigatória para entender os perigos do autoritarismo."</p>
                                <div style="color: #ffc107;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Barra lateral de compra -->
            <div class="product-sidebar">
                <!-- Seção de compra -->
                <div class="buy-section">
                    <div class="product-price">
                        <p>Preço: <strong>R$ 35,00</strong></p>
                    </div>
                    
                    <div class="product-description">
                        <p>Livro com leve desgaste nas bordas. Nenhuma página faltando.</p>
                    </div>
                    
                    <div class="product-seller">
                        <p>Vendido por: <strong>Selco Moderno</strong></p>
                    </div>
                    
                    <button class="buy-button">COMPRAR</button>
                    
                    <div class="buy-options">
                        <div class="buy-option">
                            <input type="checkbox" id="buy-later">
                            <label for="buy-later">Comprar mais tarde</label>
                        </div>
                        <div class="buy-option">
                            <a href="#"><i class="fas fa-comments"></i> Falar com vendedor</a>
                        </div>
                    </div>
                </div>
                
                <!-- Outras ofertas -->
                <div class="other-offers">
                    <h3>Outras ofertas</h3>
                    
                    <div class="offer">
                        <div class="offer-info">
                            <div class="offer-condition">Novo</div>
                            <div class="offer-seller">Livraria Cultura</div>
                            <div class="offer-price">R$ 42,90</div>
                        </div>
                        <div class="offer-actions">
                            <button class="offer-buy-btn">Comprar</button>
                        </div>
                    </div>
                    
                    <div class="offer">
                        <div class="offer-info">
                            <div class="offer-condition">Usado</div>
                            <div class="offer-seller">Sebo do Léo</div>
                            <div class="offer-price">R$ 28,50</div>
                        </div>
                        <div class="offer-actions">
                            <button class="offer-buy-btn">Comprar</button>
                        </div>
                    </div>
                    
                    <div class="offer">
                        <div class="offer-info">
                            <div class="offer-condition">Novo</div>
                            <div class="offer-seller">Amazon</div>
                            <div class="offer-price">R$ 39,90</div>
                        </div>
                        <div class="offer-actions">
                            <button class="offer-buy-btn">Comprar</button>
                        </div>
                    </div>
                    
                    <div class="more-offers">
                        <a href="#">Ver as outras 5 ofertas deste livro</a>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="footer"> 
        &copy; 2025 Entre Linhas - Todos os direitos reservados. 
    </div> 
    
    <script>
        function changeTab(index) {
            // Remove a classe active de todas as abas
            const tabs = document.querySelectorAll('.product-tabs li');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Adiciona a classe active à aba clicada
            tabs[index].classList.add('active');
            
            // Remove a classe active de todos os conteúdos
            const contents = document.querySelectorAll('.tab-content div');
            contents.forEach(content => content.classList.remove('active'));
            
            // Adiciona a classe active ao conteúdo correspondente
            contents[index].classList.add('active');
        }
    </script>
</body> 
</html>