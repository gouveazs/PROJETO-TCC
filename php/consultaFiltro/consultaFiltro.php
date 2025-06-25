<?php
include '../conexao.php';
$busca = $_POST['nome']; // ou $_GET['q'] se vier do GET

try {
    // Busca em usuários
    $stmtUsuarios = $conn->prepare("SELECT idusuario, nome, email FROM cadastro_usuario WHERE nome LIKE :busca");
    $stmtUsuarios->bindValue(':busca', "%$busca%");
    $stmtUsuarios->execute();

    // Busca em produtos (livros)
    $stmtLivros = $conn->prepare("SELECT idproduto, nome, autor, preco FROM produto WHERE nome LIKE :busca OR autor LIKE :busca");
    $stmtLivros->bindValue(':busca', "%$busca%");
    $stmtLivros->execute();

    echo '<style>
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #555; padding: 10px; text-align: left; }
        th { background-color: #8C5B3F; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
    </style>';

    // Resultados de usuários
    if ($stmtUsuarios->rowCount() > 0) {
        echo "<h2 style='text-align:center;'>Usuários Encontrados</h2>";
        echo "<table><tr><th>Código</th><th>Nome</th><th>Email</th></tr>";
        while ($row = $stmtUsuarios->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['idusuario']) . "</td>
                    <td>" . htmlspecialchars($row['nome']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                  </tr>";
        }
        echo "</table>";
    }

    // Resultados de livros
    if ($stmtLivros->rowCount() > 0) {
        echo "<h2 style='text-align:center;'>Livros/Produtos Encontrados</h2>";
        echo "<table><tr><th>Código</th><th>Nome do Livro</th><th>Autor</th><th>Preço</th></tr>";
        while ($row = $stmtLivros->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['idproduto']) . "</td>
                    <td>" . htmlspecialchars($row['nome']) . "</td>
                    <td>" . htmlspecialchars($row['autor']) . "</td>
                    <td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>
                  </tr>";
        }
        echo "</table>";
    }

    if ($stmtUsuarios->rowCount() == 0 && $stmtLivros->rowCount() == 0) {
        echo "Nenhum resultado encontrado.";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>
