<?php
session_start();
include '../conexao.php';

$nome = $_SESSION['nome_usuario'] ?? null;
if (!$nome) {
    die("Usuário não logado.");
}

// Buscar o id do usuário logado
$sqlUser = "SELECT idusuario, foto_de_perfil FROM usuario WHERE nome = :nome";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute([':nome' => $nome]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuário não encontrado.");
}

$id_usuario = $user['idusuario'];
$foto_usuario = $user['foto_de_perfil'];

$id_comunidade = $_GET['id'] ?? null;
if (!$id_comunidade) {
    die("Comunidade inválida.");
}

// Busca dados da comunidade
$sqlCom = "SELECT c.*, u.nome AS dono_nome, u.foto_de_perfil AS dono_foto
           FROM comunidades c
           JOIN usuario u ON c.idusuario = u.idusuario
           WHERE c.idcomunidades = :id_comunidade";
$stmtCom = $conn->prepare($sqlCom);
$stmtCom->execute([':id_comunidade' => $id_comunidade]);
$com = $stmtCom->fetch(PDO::FETCH_ASSOC);

if (!$com) {
    die("Comunidade não encontrada.");
}

// Contar quantidade de usuários na comunidade
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM membros_comunidade WHERE idcomunidades = :id");
$stmtCount->execute([':id' => $id_comunidade]);
$quantidade_usuarios = $stmtCount->fetchColumn();

// Verifica se usuário logado é dono
$usuario_dono = ($id_usuario == $com['idusuario']);

// Verifica se já é membro
$sqlMembro = "SELECT * FROM membros_comunidade WHERE idcomunidades = :idcomunidades AND idusuario = :idusuario";
$stmtMembro = $conn->prepare($sqlMembro);
$stmtMembro->execute([':idcomunidades' => $id_comunidade, ':idusuario' => $id_usuario]);
$ja_membro = $stmtMembro->fetch(PDO::FETCH_ASSOC);

// Se enviou o formulário e ainda não é membro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (idcomunidades, idusuario, papel) 
                  VALUES (:id_comunidade, :id_usuario, 'membro')";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario
    ]);

    // Redireciona para a mesma página para atualizar
    header("Location: ver_comunidade.php?id=$id_comunidade");
    exit;
}

// Buscar regras da comunidade
$stmtRegras = $conn->prepare("SELECT regra FROM regras_comunidade WHERE idcomunidades = :id_comunidade");
$stmtRegras->execute([':id_comunidade' => $id_comunidade]);
$regras = $stmtRegras->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($com['nome']) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($com['nome']) ?></h2>
<img src="data:image/jpeg;base64,<?= base64_encode($com['imagem']) ?>" alt="<?= htmlspecialchars($com['nome']) ?>">

<p><strong>Dono:</strong> <?= htmlspecialchars($com['dono_nome']) ?>
    <br>
    <img src="data:image/jpeg;base64,<?= base64_encode($com['dono_foto']) ?>" alt="Foto do dono" width="80">
</p>

<p><strong>Quantidade de membros:</strong> <?= $quantidade_usuarios ?></p>

<?php if ($usuario_dono): ?>
    <p style="color: green; font-weight: bold;">Você é o dono desta comunidade!</p>
<?php endif; ?>

<?php if (!empty($regras)): ?>
    <h3>Regras da Comunidade:</h3>
    <ul>
    <?php foreach($regras as $r): ?>
        <li><?= htmlspecialchars($r) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><?= nl2br(htmlspecialchars($com['descricao'])) ?></p>

<?php if (!$ja_membro): ?>
    <form method="POST">
        <input type="hidden" name="id_comunidade" value="<?= $id_comunidade ?>">
        <button type="submit">Participar da Comunidade</button>
    </form>
<?php else: ?>
    <p>Você já participa desta comunidade.</p>
    <a href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">Ir para o Chat</a>
<?php endif; ?>

</body>
</html>
