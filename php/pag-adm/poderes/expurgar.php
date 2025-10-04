<?php
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $id = (int) $_POST['id'];
    $opcao = $_POST['opcao']; // corrigido

    if ($tipo === 'usuario') {
        if ($opcao == 'desativar') {
            $stmt = $conn->prepare("UPDATE usuario SET status = 'desativado' WHERE idusuario = ?");
            $stmt->execute([$id]);    
        } elseif ($opcao == 'reativar') {
            $stmt = $conn->prepare("UPDATE usuario SET status = 'ativo' WHERE idusuario = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $conn->prepare("DELETE FROM usuario WHERE idusuario = ?");
            $stmt->execute([$id]);
        }
        
        header("Location: ../consulta-usuarios.php");
        exit;

    } elseif ($tipo === 'vendedor') {
        if ($opcao == 'desativar') {
            $stmt = $conn->prepare("UPDATE vendedor SET status = 'desativado' WHERE idvendedor = ?");
            $stmt->execute([$id]);    
        } elseif ($opcao == 'reativar') {
            $stmt = $conn->prepare("UPDATE vendedor SET status = 'ativo' WHERE idvendedor = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $conn->prepare("DELETE FROM vendedor WHERE idvendedor = ?");
            $stmt->execute([$id]);
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
    <title>Expurgar <?= htmlspecialchars($tipo) ?></title>
</head>
<body>
    <h2>Expurgar <?= ucfirst($tipo) ?></h2>

    <?php if ($tipo === 'usuario'): ?>
        <form method="post" action="expurgar.php" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="usuario">
            <input type="hidden" name="id" value="<?= $dados['idusuario'] ?>">

            <label>Nome de Usuário:</label>
            <input type="text" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" readonly><br>

            <label>Escolha:</label>
            <select name="opcao">
                <?php if ($dados['status'] === 'desativado'): ?>
                    <option value="reativar">Reativar</option>
                    <option value="excluir">Excluir</option>
                <?php else: ?>
                    <option value="desativar">Desativar</option>
                    <option value="excluir">Excluir</option>
                <?php endif; ?>
            </select><br><br>

            <button type="submit">Salvar</button>
        </form>

    <?php elseif ($tipo === 'vendedor'): ?>
        <form method="post" action="expurgar.php" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="vendedor">
            <input type="hidden" name="id" value="<?= $dados['idvendedor'] ?>">

            <label>Nome Completo:</label>
            <input type="text" value="<?= htmlspecialchars($dados['nome_completo'] ?? '') ?>" readonly><br>

            <label>Escolha:</label>
            <select name="opcao">
                <?php if ($dados['status'] === 'desativado'): ?>
                    <option value="reativar">Reativar</option>
                    <option value="excluir">Excluir</option>
                <?php else: ?>
                    <option value="desativar">Desativar</option>
                    <option value="excluir">Excluir</option>
                <?php endif; ?>
            </select><br><br>

            <button type="submit">Salvar</button>
        </form>
    <?php endif; ?>
</body>
</html>
<?php endif; ?>