<?php
session_start();
include '../conexao.php'; // usa o mesmo banco "banco"

$nome = $_SESSION['nome_usuario'] ?? null;
if (!$nome) {
    die("Usuário não logado.");
}

// Buscar o id do usuário logado
$sqlUser = "SELECT idusuario FROM usuario WHERE nome = :nome";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute([':nome' => $nome]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuário não encontrado.");
}

$id_usuario = $user['idusuario'];
$id_comunidade = $_GET['id'] ?? null;

if (!$id_comunidade) {
    die("Comunidade inválida.");
}

// Verifica se já é membro da comunidade
$sql = "SELECT * FROM membros_comunidade WHERE idcomunidades = :idcomunidades AND idusuario = :idusuario";
$stmt = $conn->prepare($sql);
$stmt->execute([':idcomunidades' => $id_comunidade, ':idusuario' => $id_usuario]);
$ja_membro = $stmt->fetch(PDO::FETCH_ASSOC);

// Se enviou formulário e ainda não é membro, insere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (idcomunidades, idusuario) VALUES (:id_comunidade, :id_usuario)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([':id_comunidade' => $id_comunidade, ':id_usuario' => $id_usuario]);

    header("Location: chat/chat.php?id_comunidade=$id_comunidade");
    exit;
}

// Busca dados da comunidade
$sqlCom = "SELECT * FROM comunidades WHERE idcomunidades = :id_comunidade";
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
    <a href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">Ir para o Chat</a>
<?php endif; ?>

</body>
</html>