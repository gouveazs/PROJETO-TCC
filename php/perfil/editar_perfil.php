<?php
session_start();
include '../conexao.php'; 

$nome_usuario = $_SESSION['nome_usuario'] ?? null;

if (!$nome_usuario) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT idusuario FROM cadastro_usuario WHERE nome = ?");
$stmt->execute([$nome_usuario]);
$dados_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$idusuario = $dados_usuario['idusuario'] ?? null;

if (!$idusuario) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];

    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        $stmt = $conn->prepare("UPDATE cadastro_usuario SET nome = ?, email = ?, foto_de_perfil = ? WHERE idusuario = ?");
        $stmt->execute([$novo_nome, $novo_email, $foto, $idusuario]);
    } else {
        $stmt = $conn->prepare("UPDATE cadastro_usuario SET nome = ?, email = ? WHERE idusuario = ?");
        $stmt->execute([$novo_nome, $novo_email, $idusuario]);
    }

    $_SESSION['nome_usuario'] = $novo_nome;
    $mensagem = "Dados atualizados com sucesso!";
    header("Location: ver_perfil.php");
}

//serve pra ver os dado antigo dentro do campin
$stmt = $conn->prepare("SELECT nome, email, foto_de_perfil FROM cadastro_usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Editar Perfil</h2>

<?php if (!empty($mensagem)) echo "<p style='color:green;'>$mensagem</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>

    <label>Foto de Perfil:</label><br>
    <?php if (!empty($usuario['foto_de_perfil'])): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_de_perfil']) ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="foto" accept="image/*"><br><br>

    <button type="submit">Salvar</button>
</form>
