<?php
include '../../conexao.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se o token existe e está válido
    $stmt = $conn->prepare("SELECT * FROM recuperacao_senha WHERE token = ? AND usado = 0 AND expira_em > NOW()");
    $stmt->execute([$token]);
    $recuperacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recuperacao) {
        // Exibir formulário para redefinir a senha
        ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <input type="password" name="nova_senha" placeholder="Nova senha" required>
            <button type="submit">Redefinir</button>
        </form>
        <?php
    } else {
        echo "Link inválido ou expirado.";
    }
}

// Quando enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM recuperacao_senha WHERE token = ? AND usado = 0 AND expira_em > NOW()");
    $stmt->execute([$token]);
    $recuperacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recuperacao) {
        if ($recuperacao['idusuario']) {
            $stmt = $conn->prepare("UPDATE usuario SET senha = ? WHERE idusuario = ?");
            $stmt->execute([$nova_senha, $recuperacao['idusuario']]);
        } else {
            $stmt = $conn->prepare("UPDATE vendedor SET senha = ? WHERE idvendedor = ?");
            $stmt->execute([$nova_senha, $recuperacao['idvendedor']]);
        }

        // Marcar token como usado
        $stmt = $conn->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE idrecuperacao_senha = ?");
        $stmt->execute([$recuperacao['idrecuperacao_senha']]);

        echo "Senha redefinida com sucesso!";
    } else {
        echo "Token inválido.";
    }
}
?>
