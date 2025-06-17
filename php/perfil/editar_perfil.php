<?php
session_start();
include '../conexao.php';

$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;

if (!$nome_usuario) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT idusuario FROM cadastro_usuario WHERE nome = ?");
$stmt->execute([$nome_usuario]);
$dados_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$idusuario = isset($dados_usuario['idusuario']) ? $dados_usuario['idusuario'] : null;

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
    header("Location: ver_perfil.php");
    exit;
}

$stmt = $conn->prepare("SELECT nome, email, foto_de_perfil FROM cadastro_usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editor Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
        }

        .profile-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            border-radius: 0;
            padding: 40px;
            box-shadow: none;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
            text-align: left;
            letter-spacing: -0.5px;
        }

        .profile-field {
            margin-bottom: 25px;
        }

        .profile-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 15px;
        }

        .profile-field input[type="text"],
        .profile-field input[type="email"] {
            width: 100%;
            padding: 8px 0;
            border: none;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            background: transparent;
            outline: none;
        }

        .file-upload-container {
            margin-top: 30px;
        }

        .file-upload-label {
            display: block;
            font-weight: 500;
            color: #333;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .file-upload-btn {
            display: inline-block;
            padding: 8px 0;
            color: #333;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }

        .no-file {
            font-size: 14px;
            color: #999;
            margin-left: 10px;
        }

        .save-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #5a6b50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 40px;
            transition: background-color 0.2s;
        }

        .save-btn:hover {
            background-color: #4a5a40;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 1px solid #eee;
        }

        input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h1>Editor Perfil</h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="profile-field">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="profile-field">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="file-upload-container">
            <label class="file-upload-label">Foto de Perfil:</label>
            <?php if (!empty($usuario['foto_de_perfil'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_de_perfil']) ?>" class="profile-pic">
            <?php endif; ?>
            
            <label class="file-upload-btn" for="foto-upload">Escolher arquivo</label>
            <input id="foto-upload" type="file" name="foto" accept="image/*">
            <span class="no-file">Nenhum arquivo escolhido</span>
        </div>

        <button type="submit" class="save-btn">Salvar</button>
    </form>
</div>

<script>
    document.getElementById('foto-upload').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Nenhum arquivo escolhido';
        document.querySelector('.no-file').textContent = fileName;
    });
</script>

</body>
</html>