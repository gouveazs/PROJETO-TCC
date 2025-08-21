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
            background-color: #F4F1EE;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
            text-align: left;
        }

        .profile-field {
            margin-bottom: 25px;
        }

        .profile-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 15px;
        }

        .profile-field input[type="text"],
        .profile-field input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
        }

        .profile-field input[type="text"]:focus,
        .profile-field input[type="email"]:focus {
            border-color: #5a6b50;
        }

        .file-upload-container {
            margin-top: 25px;
        }

        .file-upload-label {
            display: block;
            font-weight: 500;
            color: #555;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .file-upload-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #f0f0f0;
            color: #333;
            font-size: 14px;
            border-radius: 25px;
            cursor: pointer;
            margin-right: 10px;
        }

        .no-file {
            font-size: 14px;
            color: #777;
        }

        .save-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #5a6b50;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 30px;
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

        @media (max-width: 480px) {
            .profile-container {
                padding: 30px 20px;
            }
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
            
            <div style="display: flex; align-items: center;">
                <label class="file-upload-btn" for="foto-upload">Escolher arquivo</label>
                <span class="no-file">Nenhum arquivo escolhido</span>
            </div>
            <input id="foto-upload" type="file" name="foto" accept="image/*">
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