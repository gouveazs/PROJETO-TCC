<?php
session_start();
include '../conexao.php'; // conexão para o banco de usuários

$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
if (!$nome) {
    die("Usuário não logado.");
}

// Buscar o id_usuario_externo no banco de usuários
$sqlUser = "SELECT idusuario FROM cadastro_usuario WHERE nome = :nome";
$stmtUser = $conn->prepare($sqlUser); // conexão do banco de usuários
$stmtUser->execute([':nome' => $nome]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$id_usuario_externo = $user['id'] ?? 0;
$id_comunidade = $_GET['id'] ?? 0;

// Verifica se já é membro da comunidade
$sql = "SELECT * FROM membros_comunidade WHERE idcomunidade = :idcomunidade AND id_usuario_externo = :id_usuario";
$stmt = $conn->prepare($sql); // conexão do banco de comunidades
$stmt->execute([':idcomunidade' => $id_comunidade, ':id_usuario' => $id_usuario_externo]);
$ja_membro = $stmt->fetch(PDO::FETCH_ASSOC);

// Se enviou formulário e ainda não é membro, insere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (id_comunidade, id_usuario_externo) VALUES (:id_comunidade, :id_usuario)";
    $stmtInsert = $conn_comunidade->prepare($sqlInsert);
    $stmtInsert->execute([':id_comunidade' => $id_comunidade, ':id_usuario' => $id_usuario_externo]);

    header("Location: chat.php?id_comunidade=$id_comunidade");
    exit;
}

// Busca dados da comunidade
$sqlCom = "SELECT * FROM comunidades WHERE id = :id_comunidade";
$stmtCom = $conn_comunidade->prepare($sqlCom);
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
    <a href="chat.php?id_comunidade=<?= $id_comunidade ?>">Ir para o Chat</a>
<?php endif; ?>

</body>
</html>