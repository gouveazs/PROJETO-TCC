<?php
session_start();
session_destroy(); // encerra todas as variáveis da sessão
header("Location: ../../index.php"); // redireciona pra página de login
exit();
?>