<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>
<body>
    <header class="banner">📖 Entre Linhas</header>
    <nav class="menu">
        <ul>
          <li><a href="../../index.php">Início</a></li>
          <li><a href="#">Sobre</a></li>
          <li><a href="#">Serviços</a></li>
          
          <li class="push-right"></li>
      
          <li><a href="#">Entrar na conta</a></li>
          <li><a href="../cadastro/cadastroUsuario.php">Criar conta</a></li>
          <li><a href="../cadastro/cadastroVendedor.php">Quero vender</a></li>
          <li><a href="#">Consulta</a></li>
          <li><a href="../consultaFiltro/busca.php">Consulta por Nome</a></li>
          <li><a href="../cadastro/cadastroProduto.php">Cadastrar Produto</a></li>
        </ul>
    </nav>      

<main class="conteudo">
    <h1>Consulta de usuários</h1>
    <?php
        include '../conexao.php';
        $stmt = $conn->query("SELECT * FROM cadastro_usuario");
        echo '<link rel="stylesheet" href="consulta.css">';
        echo '<table border="1">';
            echo "<tr>";
                echo "<th>Código</th>";
                echo "<th>Nome</th>";
                echo "<th>Email</th>";
                echo "<th>Senha</th>";
            echo "</tr>"; 
        while ($row = $stmt->fetch()) {      
            echo "<tr>";
                echo "<td>".$row['idusuario']."</td>";
                echo "<td>".$row['nome']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['senha']."</td>";
            echo "</tr>";          
            }
        echo '</table>';
    ?>
    <h1>Consulta de vendedores</h1>
    <?php  
        include '../conexaoVendedor.php';
        $stmt = $conn->query("SELECT * FROM cadastro_vendedor");
        echo '';
        echo '<table border="1">';
            echo "<tr>";
                echo "<th>Código</th>";
                echo "<th>Nome</th>";
                echo "<th>DATA DE NASCIMENTO</th>";
                echo "<th>EMAIL</th>";
                echo "<th>SENHA</th>";
                echo "<th>CPF</th>";
                echo "<th>CNPJ</th>";
            echo "</tr>"; 
        while ($row = $stmt->fetch()) {      
            echo "<tr>";
                echo "<td>".$row['idvendedor']."</td>";
                echo "<td>".$row['nome_completo']."</td>";
                echo "<td>".$row['data_nascimento']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['senha']."</td>";
                echo "<td>".$row['cpf']."</td>";
                echo "<td>".$row['cnpj']."</td>";
            echo "</tr>";          
            }
        echo '</table>';  
    ?>
    <h1>Consulta de produto</h1>
    <?php  
        include '../conexaoVendedor.php';
        $stmt = $conn->query("SELECT * FROM produto");
        echo '<table border="1">';
            echo "<tr>";
                echo "<th>IMAGEM</th>";
                echo "<th>Código</th>";
                echo "<th>Nome DO LIVRO</th>";
                echo "<th>NÚMERO DE PÁGINAS</th>";
                echo "<th>EDITORA</th>";
                echo "<th>AUTOR</th>";
                echo "<th>CLASSIFICAÇÃO ETÁRIA</th>";
                echo "<th>DATA DE PUBLICAÇÃO</th>";
                echo "<th>PREÇO</th>";
                echo "<th>QUANTIDADE EM ESTOQUE</th>";
                echo "<th>DESCRIÇÃO</th>";
                echo "<th>ID VENDEDOR</th>";
            echo "</tr>"; 

        while ($row = $stmt->fetch()) {
            echo "<tr>";

            // Verifica se há imagem e converte para base64
            if (!empty($row['imagem'])) {
                $imgData = base64_encode($row['imagem']);
                echo '<td><img src="data:image/jpeg;base64,' . $imgData . '" width="100" height="auto"/></td>';
            } else {
                echo "<td>Sem imagem</td>";
            }

            echo "<td>".$row['idproduto']."</td>";
            echo "<td>".htmlspecialchars($row['nome'])."</td>";
            echo "<td>".$row['numero_paginas']."</td>";
            echo "<td>".htmlspecialchars($row['editora'])."</td>";
            echo "<td>".htmlspecialchars($row['autor'])."</td>";
            echo "<td>".$row['classificacao_idade']."</td>";
            echo "<td>".$row['data_publicacao']."</td>";
            echo "<td>R$ ".number_format($row['preco'], 2, ',', '.')."</td>";
            echo "<td>".$row['quantidade']."</td>";
            echo "<td>".htmlspecialchars($row['descricao'])."</td>";
            echo "<td>".$row['idvendedor']."</td>";
            
            echo "</tr>";          
        }
        echo '</table>';
    ?>
</main>

    <footer class="rodape">
        Todos os direitos reservados - 2025        
    </footer>
</body>
</html>
