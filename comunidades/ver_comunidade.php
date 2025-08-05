<?php
include '../php/conexao_comunidade.php';

$id_usuario_externo = 1; // ID do usuário logado (exemplo fixo)
$id_comunidade = $_GET['id'] ?? 0;

// Verifica se já é membro da comunidade
$sql = "SELECT * FROM membros_comunidade WHERE id_comunidade = :id_comunidade AND id_usuario_externo = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->execute([':id_comunidade' => $id_comunidade, ':id_usuario' => $id_usuario_externo]);
$ja_membro = $stmt->fetch(PDO::FETCH_ASSOC);

// Se enviou formulário e ainda não é membro, insere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (id_comunidade, id_usuario_externo) VALUES (:id_comunidade, :id_usuario)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([':id_comunidade' => $id_comunidade, ':id_usuario' => $id_usuario_externo]);

    header("Location: chat.php?id_comunidade=$id_comunidade");
    exit;
}

// Busca dados da comunidade
$sqlCom = "SELECT * FROM comunidades WHERE id = :id_comunidade";
$stmtCom = $conn->prepare($sqlCom);
$stmtCom->execute([':id_comunidade' => $id_comunidade]);
$com = $stmtCom->fetch(PDO::FETCH_ASSOC);

if (!$com) {
    die("Comunidade não encontrada.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($com['nome']) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($com['nome']) ?></h2>
<p><?= nl2br(htmlspecialchars($com['descricao'])) ?></p>

<?php if (!$ja_membro): ?>
    <form method="POST">
        <button type="submit">Participar da Comunidade</button>
    </form>
<?php else: ?>
    <p>Você já participa desta comunidade.</p>
    <a href="chat.php">Ir para o Chat</a>
<?php endif; ?>

</body>
</html>
