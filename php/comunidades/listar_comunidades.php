<?php
include '../php/conexao_comunidade.php';

// Supondo que $connComunidades seja um objeto PDO
$result = $conn->query("SELECT * FROM comunidades ORDER BY criada_em DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Comunidades</title>
</head>
<body>

<h2>Comunidades Disponíveis</h2>
<a href="criar_comunidade.php">+ Criar Nova Comunidade</a><br><br>

<ul>
<?php while ($com = $result->fetch(PDO::FETCH_ASSOC)): ?>
    <li>
        <strong><?= htmlspecialchars($com['nome']) ?></strong><br>
        <?= nl2br(htmlspecialchars($com['descricao'])) ?><br>
        <a href="ver_comunidade.php?id=<?= $com['id'] ?>">Entrar</a>
        <hr>
    </li>
<?php endwhile; ?>
</ul>

</body>
</html>
