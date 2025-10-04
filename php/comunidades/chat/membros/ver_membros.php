<?php
session_start();
include '../../../conexao.php';

$id_usuario = $_SESSION['idusuario'];
$id_comunidade = $_GET['id_comunidade'];

// Verifica se é admin
$stmt = $conn->prepare("SELECT idusuario AS id_criador FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([':id' => $id_comunidade]);
$com = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$com || $com['id_criador'] != $id_usuario) die("Apenas o administrador pode ver membros.");

// Buscar membros
$stmt = $conn->prepare("
    SELECT u.idusuario, u.nome, u.foto_de_perfil, mc.entrou_em, mc.papel
    FROM membros_comunidade mc
    JOIN usuario u ON mc.idusuario = u.idusuario
    WHERE mc.idcomunidades = :id
    ORDER BY mc.entrou_em ASC
");
$stmt->execute([':id' => $id_comunidade]);
$membros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Membros da Comunidade</title>
</head>
<body>
<h2>Membros da Comunidade</h2>
<ul>
<?php foreach($membros as $m): ?>
    <li style="margin-bottom:10px; list-style:none;">
        <img src="data:image/jpeg;base64,<?= base64_encode($m['foto_de_perfil']) ?>" width="40" style="vertical-align:middle; border-radius:50%;">
        <strong><?= htmlspecialchars($m['nome']) ?></strong> 
        <span style="color:gray;">(<?= $m['papel'] ?>)</span> - Entrou em <?= $m['entrou_em'] ?>

        <?php if ($m['idusuario'] != $com['id_criador']): ?>
            <a href="banir_membro.php?id_usuario=<?= $m['idusuario'] ?>&id_comunidade=<?= $id_comunidade ?>" 
                style="margin-left:10px; background:red; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;">
                Banir
            </a>

            <?php if ($m['papel'] == 'membro'): ?>
                <a href="promover_membro.php?id_usuario=<?= $m['idusuario'] ?>&id_comunidade=<?= $id_comunidade ?>" 
                    style="margin-left:5px; background:green; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;">
                    Promover
                </a>
            <?php elseif ($m['papel'] == 'moderador'): ?>
                <a href="demover_membro.php?id_usuario=<?= $m['idusuario'] ?>&id_comunidade=<?= $id_comunidade ?>" 
                    style="margin-left:5px; background:orange; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;">
                    Demover
                </a>
            <?php endif; ?>
        <?php endif; ?>

    </li>
<?php endforeach; ?>
</ul>

<a href="../chat.php?id_comunidade=<?= $id_comunidade ?>">Voltar ao Chat</a>
</body>
</html>
