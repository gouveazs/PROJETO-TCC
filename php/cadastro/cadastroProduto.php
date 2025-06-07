<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entre Linhas</title>
    <link rel="stylesheet" href="../../css/estilo.css">
    <link rel="stylesheet" href="../../css/estilo2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>

    <header class="banner">
        Entre Linhas
        <img src="../../imgs/pilha-de-tres-livros.png" class="custom-icon" alt="Logo Livros">
    </header>
    
    <nav class="menu">
        <ul>
            <li class="push-right"></li>
            <li><a href="#">Entrar na conta</a></li>
            <li><a href="#">Criar conta</a></li>
            <li><a href="#">Quero vender</a></li>
            <li><a href="#">Consulta</a></li>
            <li><a href="#">Consulta por Nome</a></li>
            <li><a href="#">Cadastrar Produto</a></li>
        </ul>
    </nav>

    <main class="conteudo" style="max-width:800px; margin:100px auto 40px auto; padding:20px; text-align:center; display:block;">
        <h1>Cadastro de Produto</h1>
        <form action="../insercao/insercaoProduto.php" method="post" enctype="multipart/form-data">
            <p>Nome do produto:</p>
            <input type="text" name="nome">
            
            <p>Número de páginas:</p>
            <input type="number" name="numero_paginas">

            <p>Editora:</p>
            <input type="text" name="editora">

            <p>Autor:</p>
            <input type="text" name="autor">

            <p>Classificação etária:</p>
            <input type="number" name="classificacao_idade">

            <p>Data de publicação:</p>
            <input type="date" name="data_publicacao">

            <p>Preço (R$):</p>
            <input type="number" step="0.01" name="preco">

            <p>Quantidade em estoque:</p>
            <input type="number" name="quantidade">

            <p>Descrição:</p>
            <textarea name="descricao" rows="4"></textarea>

            <p>Imagem do produto:</p>
            <input type="file" name="imagem" >
            <br>
            <input type="submit" style="margin-top: 30px;" value="Cadastrar">
        </form>
    </main>     

    <footer class="rodape">
        Todos os direitos reservados - 2025
    </footer>

</body>
</html>