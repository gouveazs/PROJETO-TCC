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

// Se enviou o formulário de entrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'entrar' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (idcomunidades, idusuario, papel) 
                  VALUES (:id_comunidade, :id_usuario, 'membro')";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario
    ]);

    header("Location: ver_comunidade.php?id=$id_comunidade");
    exit;
}

// Se enviou o formulário de sair
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'sair' && $ja_membro) {
    $sqlDelete = "DELETE FROM membros_comunidade WHERE idcomunidades = :id_comunidade AND idusuario = :id_usuario";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario
    ]);

    header("Location: comunidade.php"); // volta para lista de comunidades
    exit;
}

// Buscar regras da comunidade
$stmtRegras = $conn->prepare("SELECT regra FROM regras_comunidade WHERE idcomunidades = :id_comunidade");
$stmtRegras->execute([':id_comunidade' => $id_comunidade]);
$regras = $stmtRegras->fetchAll(PDO::FETCH_COLUMN);

// Verifica se usuário está banido
$stmtBan = $conn->prepare("SELECT * FROM banimentos_comunidade WHERE idcomunidades = :id AND idusuario = :user");
$stmtBan->execute([':id' => $id_comunidade, ':user' => $id_usuario]);
$banido = $stmtBan->fetch(PDO::FETCH_ASSOC);

if ($banido) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($com['nome']) ?></title>
    </head>
    <body>
        <h2><?= htmlspecialchars($com['nome']) ?></h2>
        <p style="color:red; font-weight:bold; font-size:18px;">
            🚫 Você foi banido desta comunidade para todo o sempre hahaha 😈
        </p>
        <p><strong>Motivo:</strong> <?= htmlspecialchars($banido['motivo']) ?></p>
        <a href="comunidade.php">Voltar</a>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($com['nome']) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($com['nome']) ?></h2>
<p><strong>Status da Comunidade:</strong> 
   <?= $com['status'] === 'ativa' ? '✅ Ativa' : '⛔ Desativada' ?>
</p>

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
        <input type="hidden" name="acao" value="entrar">
        <button type="submit">Participar da Comunidade</button>
    </form>
<?php else: ?>
    <p>✅ Você já participa desta comunidade.</p>
    <p><strong>Seu papel:</strong> <?= htmlspecialchars($ja_membro['papel']) ?></p>

    <a href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">Ir para o Chat</a>

    <?php if (!$usuario_dono): ?>
        <form method="POST" style="margin-top:10px;">
            <input type="hidden" name="acao" value="sair">
            <button type="submit" style="background:red; color:white; padding:5px 10px; border:none; border-radius:5px;">
                Sair da Comunidade
            </button>
        </form>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
