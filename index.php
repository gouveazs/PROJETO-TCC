<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entre Linhas</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/estilo2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header class="banner">Entre Linhas
    <img src="imgs/pilha-de-tres-livros.png" class="custom-icon">
    </header>
    
    <nav class="menu">
        <ul>
            <!-- This li pushes the next items to the right -->
            <li class="push-right"></li>
            <li><a href="login/login.php">Entrar na conta</a></li>
            <li><a href="php/cadastro/cadastroUsuario.php">Criar conta</a></li>
            <li><a href="php/cadastro/cadastroVendedor.php">Quero vender</a></li>

            <!-- Sessão de adms -->
            <?php if ($nome === 'adm'): ?>
                <li><a href="php/consulta/consulta.php">Consulta</a></li>
                <li><a href="php/consultaFiltro/busca.php">Consulta por Nome</a></li>
                <li><a href="php/cadastro/cadastroProduto.php">Cadastrar Produto</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <nav id="sidebar">
        <div id="sidebar_content">
            <div id="user">
                <img src="imgs/imagem-do-usuario-com-fundo-preto.png" id="user_avatar" alt="Avatar" class="custom-icon.user">
    
                <p id="user_infos">
                    <?php if ($nome): ?>
                        <span class="item-description"><?php echo htmlspecialchars($nome); ?></span>
                        <span class="item-description">Usuário</span>
                    <?php else: ?>
                        <span class="item-description">Usuário</span>
                        <span class="item-description">Entre ou crie sua conta</span>
                    <?php endif; ?>
                </p>
            </div>
    
            <ul id="side_items">
                <li class="side-item active">
                    <img src="imgs/botao-de-inicio.png" class="custom-icon">
                        <span class="item-description">Início</span>
                    </a>
                </li>
    
                <li class="side-item">
                 <img src="imgs/comunidade.png" class="custom-icon">
                     <span class="item-description"><a href="comunidades/listar_comunidades.php">Comunidades</a></span>
                    </a>
                </li>

                    <li class="side-item">
                 <img src="imgs/carrinho-carrinho.png" class="custom-icon">
                     <span class="item-description">Carrinho</span>
                    </a>
                </li>
    
                <li class="side-item">
                 <img src="imgs/queimar.png" class="custom-icon">
                     <span class="item-description">Destaque</span>
                    </a>
                </li>
    
                <li class="side-item">
                 <img src="imgs/gostar.png" class="custom-icon">
                     <span class="item-description">Favoritos</span>
                    </a>
                </li>
    
               <li class="side-item">
                 <img src="imgs/info.png" class="custom-icon">
                     <span class="item-description">Serviços</span>
                    </a>
                </li>
            </ul>
        </div>

        <div id="logout">
            <button id="logout_btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="item-description"><a href="login/logout.php">Sair</a></span>
            </button>
        </div>
    </nav>

    <main class="conteudo">
        <h1>Hello World!</h1>
        <p>Este é um layout</p>        
    </main>

    <footer class="rodape">
        Todos os direitos reservados - 2025        
    </footer>

    <script src="src/javascript/script.js"></script>
</body>
</html>