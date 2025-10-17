<?php
session_start();

if (!empty($_POST['cep'])) {
    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']); // só números
    $_SESSION['cep_usuario'] = $cep;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
