<?php
session_start();
include '../conexao.php';

$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

if (!$nome_usuario) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE nome = ?");
$stmt->execute([$nome_usuario]);
$dados_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$idusuario = isset($dados_usuario['idusuario']) ? $dados_usuario['idusuario'] : null;

if (!$idusuario) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_completo = $_POST['nome_completo'];
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
    $cidade = $_POST['cidade'];
    $estado = $_POST['uf'];
    $rua = $_POST['logradouro'];
    $bairro = $_POST['bairro'];
    
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto_de_perfil = file_get_contents($_FILES['foto']['tmp_name']);
        $stmt = $conn->prepare(
            "UPDATE usuario SET nome_completo = ?, nome = ?, email = ?, cpf = ?, cep = ?, cidade = ?, estado = ?, rua = ?, bairro = ?, foto_de_perfil = ? WHERE idusuario = ?"
        );
        $stmt->execute([$nome_completo ,$novo_nome, $novo_email, $cpf, $cep, $cidade, $estado, $rua, $bairro, $foto_de_perfil, $idusuario]);
    } else {
        $stmt = $conn->prepare(
            "UPDATE usuario SET nome_completo = ?, nome = ?, email = ?, cpf = ?, cep = ?, cidade = ?, estado = ?, rua = ?, bairro = ? WHERE idusuario = ?"
        );
        $stmt->execute([$nome_completo, $novo_nome, $novo_email, $cpf, $cep, $cidade, $estado, $rua, $bairro, $idusuario]);
    }

    $_SESSION['nome_usuario'] = $novo_nome;
    $_SESSION['foto_de_perfil'] = $foto_de_perfil;
    header("Location: ver_perfil.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Edite seu Perfil - Entre Linhas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
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
            <label>Nome completo:</label>
            <input type="text" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo'] ?? '') ?>" required>
        </div>

        <div class="profile-field">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="profile-field">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
        </div>

        <div class="profile-field">
            <label>CPF:</label>
            <input type="text" name="cpf" id="cpf" value="<?= htmlspecialchars($usuario['cpf'] ?? '') ?>" required>
        </div>

        <div class="profile-field">
            <label>CEP:</label>
            <input type="text" name="cep" id="cep" value="<?= htmlspecialchars($usuario['cep'] ?? '') ?>" required>
        </div>

        <div class="profile-field">
            <label>Estado:</label>
            <input type="text" name="uf" id="uf" value="<?= htmlspecialchars($usuario['estado'] ?? '')?>" readonly>
        </div>

        <div class="profile-field">
            <label>Cidade:</label>
            <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($usuario['cidade'] ?? '') ?>" readonly>
        </div>

        <div class="profile-field">
            <label>Bairro:</label>
            <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($usuario['bairro'] ?? '') ?>" readonly>
        </div>

        <div class="profile-field">
            <label>Rua:</label>
            <input type="text" name="logradouro" id="logradouro" value="<?= htmlspecialchars($usuario['rua'] ?? '') ?>" readonly>
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

    document.getElementById('cep').addEventListener('blur', async function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        const resp = await fetch(`busca_cep.php?cep=${cep}`);
        const data = await resp.json();
        if (!data.erro) {
        document.getElementById('cidade').value = data.localidade;
        document.getElementById('uf').value = data.uf;
        document.getElementById('bairro').value = data.bairro;
        document.getElementById('logradouro').value = data.logradouro;
        } else {
        alert('CEP não encontrado');
        }
    }
    });
</script>

</body>
</html>