<?php
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ======= Dados do formulário =======
    $nome_completo = trim($_POST['nomeV']);
    $data_nascimento = $_POST['data_nascimento'];
    $email_vendedor = trim($_POST['emailV']);
    $senha_vendedor = $_POST['senhaV'];
    $cpf = $_POST['cpf'];
    $cnpj = $_POST['cnpj'] ?? null;

    // ======= Sanitização do CPF =======
    // Remove pontos e traços e garante no máximo 11 dígitos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $cpf = substr($cpf, 0, 11);

    // ======= Criptografar a senha =======
    $senhaHash = password_hash($senha_vendedor, PASSWORD_DEFAULT);

    // ======= Verifica duplicidade =======
    $sqlCheck = "SELECT COUNT(*) FROM vendedor WHERE nome_completo = :nome_completo OR email = :email";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':nome_completo', $nome_completo);
    $stmtCheck->bindParam(':email', $email_vendedor);
    $stmtCheck->execute();
    $existe = $stmtCheck->fetchColumn();

    if ($existe > 0) {
        header("Location: ../cadastro/cadastroVendedor.php?erro=nome_ou_email");
        exit();
    }

    // ======= Foto de perfil (opcional) =======
    $foto_de_perfil = null;
    if (isset($_FILES['foto_de_perfil']) && $_FILES['foto_de_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto_de_perfil = file_get_contents($_FILES['foto_de_perfil']['tmp_name']);
    }

    // ======= Inserção no banco =======
    try {
        $sql = "INSERT INTO vendedor (nome_completo, data_nascimento, email, senha, cpf, cnpj, foto_de_perfil)
                VALUES (:nome_completo, :data_nascimento, :email, :senha, :cpf, :cnpj, :foto_de_perfil)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome_completo', $nome_completo);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':email', $email_vendedor);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':foto_de_perfil', $foto_de_perfil, PDO::PARAM_LOB);

        $stmt->execute();

        // ======= Redireciona para login =======
        header("Location: ../login/loginVendedor.php");
        exit();

    } catch (PDOException $e) {
        echo "<p style='color:red; text-align:center;'>Erro ao cadastrar vendedor: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    $conn = null;
}
?>
