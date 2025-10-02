<?php
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----- SALVAR ALTERAÇÕES -----
    $tipo = $_POST['tipo'];
    $id = (int) $_POST['id'];

    // Upload da foto (se enviada)
    $foto = null;
    if (isset($_FILES['foto_de_perfil']) && $_FILES['foto_de_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto_de_perfil']['tmp_name']);
    }

    if ($tipo === 'usuario') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $nome_completo = $_POST['nome_completo'];
        $telefone = $_POST['telefone'];
        $cpf = $_POST['cpf'];
        $cep = $_POST['cep'];
        $estado = $_POST['estado'];
        $cidade = $_POST['cidade'];
        $bairro = $_POST['bairro'];
        $rua = $_POST['rua'];
        $numero = $_POST['numero'];

        if ($foto !== null) {
            $stmt = $conn->prepare("UPDATE usuario 
                SET nome = ?, email = ?, nome_completo = ?, telefone = ?, cpf = ?, cep = ?, estado = ?, cidade = ?, bairro = ?, rua = ?, numero = ?, foto_de_perfil = ?
                WHERE idusuario = ?");
            $stmt->execute([$nome, $email, $nome_completo, $telefone, $cpf, $cep, $estado, $cidade, $bairro, $rua, $numero, $foto, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE usuario 
                SET nome = ?, email = ?, nome_completo = ?, telefone = ?, cpf = ?, cep = ?, estado = ?, cidade = ?, bairro = ?, rua = ?, numero = ?
                WHERE idusuario = ?");
            $stmt->execute([$nome, $email, $nome_completo, $telefone, $cpf, $cep, $estado, $cidade, $bairro, $rua, $numero, $id]);
        }

        header("Location: ../consulta-usuarios.php");
        exit;

    } elseif ($tipo === 'vendedor') {
        $nome_completo = $_POST['nome_completo'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $cnpj = $_POST['cnpj'];
        $cep = $_POST['cep'];
        $estado = $_POST['estado'];
        $cidade = $_POST['cidade'];
        $bairro = $_POST['bairro'];
        $rua = $_POST['rua'];
        $numero = $_POST['numero'];
        $status = $_POST['status'];
        $reputacao = $_POST['reputacao'];

        if ($foto !== null) {
            $stmt = $conn->prepare("UPDATE vendedor 
                SET nome_completo = ?, email = ?, cpf = ?, cnpj = ?, cep = ?, estado = ?, cidade = ?, bairro = ?, rua = ?, numero = ?, status = ?, reputacao = ?, foto_de_perfil = ?
                WHERE idvendedor = ?");
            $stmt->execute([$nome_completo, $email, $cpf, $cnpj, $cep, $estado, $cidade, $bairro, $rua, $numero, $status, $reputacao, $foto, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE vendedor 
                SET nome_completo = ?, email = ?, cpf = ?, cnpj = ?, cep = ?, estado = ?, cidade = ?, bairro = ?, rua = ?, numero = ?, status = ?, reputacao = ?
                WHERE idvendedor = ?");
            $stmt->execute([$nome_completo, $email, $cpf, $cnpj, $cep, $estado, $cidade, $bairro, $rua, $numero, $status, $reputacao, $id]);
        }

        header("Location: ../consulta-vendedores.php");
        exit;
    }

} else {
    // ----- MOSTRAR FORMULÁRIO -----
    if (!isset($_GET['tipo']) || !isset($_GET['id'])) {
        die("Parâmetros inválidos.");
    }

    $tipo = $_GET['tipo'];
    $id = (int) $_GET['id'];

    if ($tipo === 'usuario') {
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE idusuario = ?");
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dados) die("Usuário não encontrado.");
    } elseif ($tipo === 'vendedor') {
        $stmt = $conn->prepare("SELECT * FROM vendedor WHERE idvendedor = ?");
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dados) die("Vendedor não encontrado.");
    } else {
        die("Tipo inválido.");
    }
}
?>
<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar <?= htmlspecialchars($tipo) ?></title>
</head>
<body>
    <h2>Editar <?= ucfirst($tipo) ?></h2>

    <?php if ($tipo === 'usuario'): ?>
        <form method="post" action="editar.php" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="usuario">
            <input type="hidden" name="id" value="<?= $dados['idusuario'] ?>">

            <label>Nome de Usuário:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>"><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($dados['email'] ?? '') ?>"><br>

            <label>Nome Completo:</label>
            <input type="text" name="nome_completo" value="<?= htmlspecialchars($dados['nome_completo'] ?? '') ?>"><br>

            <label>Telefone:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>"><br>

            <label>CPF:</label>
            <input type="text" name="cpf" value="<?= htmlspecialchars($dados['cpf'] ?? '') ?>"><br>

            <label>CEP:</label>
            <input type="text" name="cep" value="<?= htmlspecialchars($dados['cep'] ?? '') ?>"><br>

            <label>Estado:</label>
            <input type="text" name="estado" value="<?= htmlspecialchars($dados['estado'] ?? '') ?>"><br>

            <label>Cidade:</label>
            <input type="text" name="cidade" value="<?= htmlspecialchars($dados['cidade'] ?? '') ?>"><br>

            <label>Bairro:</label>
            <input type="text" name="bairro" value="<?= htmlspecialchars($dados['bairro'] ?? '') ?>"><br>

            <label>Rua:</label>
            <input type="text" name="rua" value="<?= htmlspecialchars($dados['rua'] ?? '') ?>"><br>

            <label>Número:</label>
            <input type="text" name="numero" value="<?= htmlspecialchars($dados['numero'] ?? '') ?>"><br>

            <label>Foto de Perfil:</label><br>
            <?php if (!empty($dados['foto_de_perfil'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($dados['foto_de_perfil']) ?>" width="100"><br>
            <?php endif; ?>
            <input type="file" name="foto_de_perfil"><br><br>

            <button type="submit">Salvar</button>
        </form>

    <?php elseif ($tipo === 'vendedor'): ?>
        <form method="post" action="editar.php" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="vendedor">
            <input type="hidden" name="id" value="<?= $dados['idvendedor'] ?>">

            <label>Nome Completo:</label>
            <input type="text" name="nome_completo" value="<?= htmlspecialchars($dados['nome_completo'] ?? '') ?>"><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($dados['email'] ?? '') ?>"><br>

            <label>CPF:</label>
            <input type="text" name="cpf" value="<?= htmlspecialchars($dados['cpf'] ?? '') ?>"><br>

            <label>CNPJ:</label>
            <input type="text" name="cnpj" value="<?= htmlspecialchars($dados['cnpj'] ?? '') ?>"><br>

            <label>CEP:</label>
            <input type="text" name="cep" value="<?= htmlspecialchars($dados['cep'] ?? '') ?>"><br>

            <label>Estado:</label>
            <input type="text" name="estado" value="<?= htmlspecialchars($dados['estado'] ?? '') ?>"><br>

            <label>Cidade:</label>
            <input type="text" name="cidade" value="<?= htmlspecialchars($dados['cidade'] ?? '') ?>"><br>

            <label>Bairro:</label>
            <input type="text" name="bairro" value="<?= htmlspecialchars($dados['bairro'] ?? '') ?>"><br>

            <label>Rua:</label>
            <input type="text" name="rua" value="<?= htmlspecialchars($dados['rua'] ?? '') ?>"><br>

            <label>Número:</label>
            <input type="text" name="numero" value="<?= htmlspecialchars($dados['numero'] ?? '') ?>"><br>

            <label>Reputação:</label>
            <input type="number" step="0.01" name="reputacao" value="<?= htmlspecialchars($dados['reputacao']) ?>"><br>

            <label>Foto de Perfil:</label><br>
            <?php if (!empty($dados['foto_de_perfil'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($dados['foto_de_perfil']) ?>" width="100"><br>
            <?php endif; ?>
            <input type="file" name="foto_de_perfil"><br><br>

            <button type="submit">Salvar</button>
        </form>
    <?php endif; ?>
</body>
</html>
<?php endif; ?>