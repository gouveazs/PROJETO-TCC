<?php
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $user = null;
    $vendedor = null;

    // Procurar no usuario
    $stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Procurar no vendedor se não achou em usuario
    if (!$user) {
        $stmt = $conn->prepare("SELECT idvendedor FROM vendedor WHERE email = ?");
        $stmt->execute([$email]);
        $vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($user || $vendedor) {
        // Gerar token seguro
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        if ($user) {
            $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idusuario) VALUES (?, ?, ?)");
            $stmt->execute([$token, $expira, $user['idusuario']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idvendedor) VALUES (?, ?, ?)");
            $stmt->execute([$token, $expira, $vendedor['idvendedor']]);
        }

        $link = "https://projetosetim.com.br/2025/php3/php/perfil-usuario/protocolo-senha/resetar_senha.php?token=$token";

        mail($email, "Recuperação de senha", "Clique aqui para redefinir sua senha: $link");

        $mensagem = "Um link de recuperação foi enviado para seu email.";
        echo "Use este link para redefinir sua senha: <a href='$link'>$link</a>";
    } else {
        $mensagem = "Email não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha</title>
</head>
<body>
    <h2>Recuperar Senha</h2>

    <?php if (!empty($mensagem)) : ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <label for="email">Digite seu e-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>
        <button type="submit">Enviar link de recuperação</button>
    </form>
</body>
</html>