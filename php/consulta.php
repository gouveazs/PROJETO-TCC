<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header class="banner">📖 Entre Linhas</header>
    <nav class="menu">
        <ul>
          <li><a href="#">Início</a></li>
          <li><a href="#">Sobre</a></li>
          <li><a href="#">Serviços</a></li>
          
          <!-- Este li empurra os próximos para a direita -->
          <li class="push-right"></li>
      
          <li><a href="#">Entrar na conta</a></li>
          <li><a href="php/cadastro.php">Criar conta</a></li>
          <li><a href="php/consulta.php">Consulta</a></li>
        </ul>
      </nav>      

    <main class="conteudo">
    <?php
    include 'conexao.php';
    $stmt = $conn->query("SELECT * FROM cadastro_usuario");
    echo '
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #555;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #8C5B3F;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
';
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
    </main>

    <footer class="rodape">
        Todos os direitos reservados - 2025        
    </footer>
</body>
</html>
