<?php
include '../conexao.php';
$nome = $_POST['nome'];

try {
    $stmt = $conn->prepare("SELECT * FROM cadastro_usuario WHERE nome = :nome");
    $stmt->bindParam(':nome', $nome);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
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
        echo "<table border='1'>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Senha</th>
                    <!-- Adicione mais colunas conforme necessário -->
                </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                    <td>" . htmlspecialchars($row['idusuario']) . "</td>
                    <td>" . htmlspecialchars($row['nome']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['senha']) . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>
