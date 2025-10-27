<?php
session_start();
include '../conexao.php';

$nome = $_SESSION['nome_usuario'] ?? null;
if (!$nome) {
    die("Usuário não logado.");
}

// Buscar o id do usuário logado
$sqlUser = "SELECT idusuario, foto_de_perfil FROM usuario WHERE nome = :nome";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute([':nome' => $nome]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuário não encontrado.");
}

$id_usuario = $user['idusuario'];
$foto_usuario = $user['foto_de_perfil'];

$id_comunidade = $_GET['id'] ?? null;
if (!$id_comunidade) {
    die("Comunidade inválida.");
}

// Busca dados da comunidade
$sqlCom = "
  SELECT 
    c.*, 
    u.nome AS dono_nome, 
    u.foto_de_perfil AS dono_foto,
    cat.nome AS categoria_nome
  FROM comunidades c
  JOIN usuario u ON c.idusuario = u.idusuario
  JOIN categoria cat ON c.idcategoria = cat.idcategoria
  WHERE c.idcomunidades = :id_comunidade
";
$stmtCom = $conn->prepare($sqlCom);
$stmtCom->execute([':id_comunidade' => $id_comunidade]);
$com = $stmtCom->fetch(PDO::FETCH_ASSOC);

// Buscar moderadores
$sqlMods = "
SELECT u.nome 
FROM membros_comunidade mc
JOIN usuario u ON mc.idusuario = u.idusuario
WHERE mc.idcomunidades = :id_comunidade AND mc.papel = 'moderador'
";
$stmtMods = $conn->prepare($sqlMods);
$stmtMods->execute([':id_comunidade' => $id_comunidade]);
$moderadores_lista = $stmtMods->fetchAll(PDO::FETCH_COLUMN);

// Formatar moderadores como uma lista separada por vírgula
$moderadores_nomes = !empty($moderadores_lista) ? implode(', ', $moderadores_lista) : 'nenhum';


if (!$com) {
    die("Comunidade não encontrada.");
}

// Contar quantidade de usuários na comunidade
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM membros_comunidade WHERE idcomunidades = :id");
$stmtCount->execute([':id' => $id_comunidade]);
$quantidade_usuarios = $stmtCount->fetchColumn();

// Verifica se usuário logado é dono
$usuario_dono = ($id_usuario == $com['idusuario']);

// Verifica se já é membro
$sqlMembro = "SELECT * FROM membros_comunidade WHERE idcomunidades = :idcomunidades AND idusuario = :idusuario";
$stmtMembro = $conn->prepare($sqlMembro);
$stmtMembro->execute([':idcomunidades' => $id_comunidade, ':idusuario' => $id_usuario]);
$ja_membro = $stmtMembro->fetch(PDO::FETCH_ASSOC);

// Se enviou o formulário de entrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'entrar' && !$ja_membro) {
    $sqlInsert = "INSERT INTO membros_comunidade (idcomunidades, idusuario, papel) 
                  VALUES (:id_comunidade, :id_usuario, 'membro')";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario
    ]);

    header("Location: ver_comunidade.php?id=$id_comunidade");
    exit;
}

// Se enviou o formulário de sair
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'sair' && $ja_membro) {
    $sqlDelete = "DELETE FROM membros_comunidade WHERE idcomunidades = :id_comunidade AND idusuario = :id_usuario";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario
    ]);

    header("Location: comunidade.php"); // volta para lista de comunidades
    exit;
}

// Buscar regras da comunidade
$stmtRegras = $conn->prepare("SELECT regra FROM regras_comunidade WHERE idcomunidades = :id_comunidade");
$stmtRegras->execute([':id_comunidade' => $id_comunidade]);
$regras = $stmtRegras->fetchAll(PDO::FETCH_COLUMN);

// Buscar membros reais da comunidade (amostra)
$sqlMembros = "SELECT u.nome, u.foto_de_perfil 
               FROM membros_comunidade mc 
               JOIN usuario u ON mc.idusuario = u.idusuario 
               WHERE mc.idcomunidades = :id_comunidade 
               LIMIT 9";
$stmtMembros = $conn->prepare($sqlMembros);
$stmtMembros->execute([':id_comunidade' => $id_comunidade]);
$membros = $stmtMembros->fetchAll(PDO::FETCH_ASSOC);

// Verifica se usuário está banido
$stmtBan = $conn->prepare("SELECT * FROM banimentos_comunidade WHERE idcomunidades = :id AND idusuario = :user");
$stmtBan->execute([':id' => $id_comunidade, ':user' => $id_usuario]);
$banido = $stmtBan->fetch(PDO::FETCH_ASSOC);

if ($banido) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
      <meta charset="UTF-8">
      <title><?= htmlspecialchars($com['nome']) ?></title>
      <style>
        body { font-family: Arial, sans-serif; background:#f3eade; text-align:center; padding-top:80px; }
        .card { background:white; padding:20px; border-radius:8px; width:90%; max-width:600px; margin:0 auto; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        a { color:#3b6b3b; text-decoration:none; font-weight:bold; display:inline-block; margin-top:12px; }
      </style>
    </head>
    <body>
      <div class="card">
        <h2><?= htmlspecialchars($com['nome']) ?></h2>
        <p style="color:#c0392b; font-weight:bold;">🚫 Você foi banido desta comunidade</p>
        <p><strong>Motivo:</strong> <?= htmlspecialchars($banido['motivo']) ?></p>
        <a href="comunidade.php">Voltar para Comunidades</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($com['nome']) ?> — Comunidade</title>
  <!-- Fonte -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    /* ===== ENTRE LINHAS - estilo Orkut-like com fundo bege texturizado ===== */
    :root{
      --marrom:#5a4224;
      --verde:#a8c4a8; /* VERDE CLARINHO */
      --bege:#f5efe3;
      --offwhite:#fffdf9;
      --border:#e0d7c8;
      --shadow: rgba(0,0,0,0.08);
    }

    /* textura leve no fundo via gradiente */
    body{
      margin:0;
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background-color: #F4F1EE;
      background-image:
        linear-gradient(transparent 0.5px, rgba(0,0,0,0.01) 0.5px),
        linear-gradient(90deg, transparent 0.5px, rgba(0,0,0,0.01) 0.5px);
      background-size: 10px 10px, 10px 10px;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      color:#333;
      min-height:100vh;
    }

    /* top bar (fina, igual Orkut) */
    .topbar{
      background: linear-gradient(#fffdf9, #f0eadf);
      border-bottom: 1px solid var(--border);
      padding:10px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      box-shadow: 0 1px 0 rgba(0,0,0,0.02);
    }

    /* container central - ocupa largura com margem */
    .wrap{
      width:95%;
      max-width:1220px;
      margin:18px auto;
      display:grid;
      grid-template-columns: 220px 1fr 300px; /* left sidebar, main, right members */
      gap:18px;
    }

    /* caixa branca com borda arredondada como no orkut */
    .card{
      background:var(--offwhite);
      border:1px solid var(--border);
      border-radius:8px;
      padding:14px;
      box-shadow: 0 6px 18px var(--shadow);
    }

    /* ESQUERDA: menu (foto + links) - AJUSTADO PARA CENTRALIZAR */
    .left .profile-pic{
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      text-align: center;
    }

    .left img.avatar{
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
      border: 3px solid #cfc2a8;
      box-shadow: 0 4px 10px rgba(0,0,0,0.06);
      margin: 0 auto; /* Centraliza horizontalmente */
      display: block; /* Necessário para margin auto funcionar */
    }
    .left .menu{
      margin-top:12px;
    }

    .left .menu a{
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 10px;
      color:var(--marrom);
      text-decoration:none;
      border-radius:6px;
      margin-bottom:6px;
      font-weight:600;
      background: linear-gradient(180deg, rgba(255,255,255,0.6), transparent);
      border:1px solid rgba(0,0,0,0.03);
    }

    .left .menu a svg{ opacity:0.9; }
    .left .menu a:hover{
      background: linear-gradient(180deg, rgba(90,66,36,0.06), rgba(90,66,36,0.02));
      transform: translateX(3px);
    }

    /* CENTRO: main content parecido com a foto */
    .center .community-title{
      display:flex;
      align-items:flex-start;
      gap:16px;
    }

    .center h1{
      font-size:22px;
      color:var(--marrom);
      margin:0;
      margin-top:4px;
    }

    .center .subtitle{
      font-size:13px;
      color:#666;
      margin-top:8px;
    }

    /* tabela de infos estilo orkut: COM FUNDO VERDE CLARINHO */
    .info-table{
      width:100%;
      border-collapse:collapse;
      margin-top:12px;
      border-radius:6px;
      overflow:hidden;
      border:1px solid #9bb59b; /* borda verde */
    }

    .info-table tr:nth-child(odd) td{ 
      background: linear-gradient(#e8f3e8, #dceddc); /* verde clarinho mais claro */
    }
    .info-table tr:nth-child(even) td{ 
      background: linear-gradient(#f0f7f0, #e8f3e8); /* verde clarinho mais suave */
    }

    .info-table td{
      padding:10px 12px;
      border-bottom:1px solid #cde0cd; /* borda entre linhas em verde */
      font-size:14px;
    }

    /* imagem grande da comunidade como na foto */
    .community-img{
      width: 100%; /* Ocupa toda a largura disponível */
      height: 280px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #d9d0bf;
      margin: 14px 0;
      box-shadow: 0 6px 12px rgba(0,0,0,0.06);
    }
    /* AÇÕES (participar/chat/sair) */
    .actions{
      margin-top:12px;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn{
      background: linear-gradient(180deg, #7a5b3b, #5a4224);
      color:white;
      padding:8px 14px;
      border-radius:6px;
      text-decoration:none;
      border:1px solid rgba(0,0,0,0.06);
      font-weight:600;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }

    .btn.alt{
      background: linear-gradient(180deg, #5a6b50, #49603f);
    }

    .btn.ghost{
      background:transparent;
      color:var(--marrom);
      border:1px solid #d9d0bf;
    }

    /* REGRAS */
    .rules{
      margin-top:10px;
      padding:10px;
      background:linear-gradient(180deg,#fffaf0,#fbf6ec);
      border-radius:6px;
      border:1px solid #efe6d8;
    }

    /* DIREITA: membros grid (pequenas miniaturas) */
    .right .members-title{
      font-weight:700;
      color:var(--marrom);
      font-size:14px;
      margin-bottom:10px;
    }

    .members-grid{
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:8px;
    }

    .member-box{
      background:linear-gradient(180deg,#fff,#fbf8f3);
      border:1px solid #e7dfcf;
      border-radius:6px;
      padding:8px;
      text-align:center;
    }

    .member-box img{
      width:62px;
      height:62px;
      object-fit:cover;
      border-radius:4px;
      border:1px solid #d7ccb6;
      margin-bottom:6px;
    }

    .member-name{ font-size:13px; color:var(--marrom); font-weight:600; }

    /* rodapé pequeno */
    .small-note{
      font-size:12px;
      color:#7b6a58;
      margin-top:8px;
    }

    /* responsividade */
    @media (max-width:1000px){
      .wrap{ grid-template-columns: 200px 1fr; }
      .right{ display:none; } /* esconde a coluna direita em telas médias */
    }

    @media (max-width:700px){
      .wrap{ grid-template-columns: 1fr; padding:10px; }
      .left{ order:2; }
      .center{ order:1; }
      .topbar{ padding:8px; }
      .left img.avatar{ width:96px; height:96px; }
    }
  </style>
</head>
<body>

<!-- Top bar -->
<div class="topbar">
  <div style="display:flex;align-items:center;gap:16px;">
    <div style="font-weight:700;color:var(--marrom);">Entre Linhas</div>
    <div style="font-size:13px;color:#6b5a47;">Comunidades</div>
  </div>
  <div style="display:flex;gap:12px;align-items:center;">
    <div style="font-size:13px;color:#6b5a47;">Olá, <?= htmlspecialchars($nome) ?></div>
    <a href="../php/login/logout.php" style="text-decoration:none;color:var(--marrom);font-weight:600;">Sair</a>
  </div>
</div>

<!-- Main wrap com 3 colunas similar à imagem -->
<div class="wrap">

  <!-- LEFT: sidebar com foto e links (estilo orkut) -->
  <div class="left card">
    <div class="profile-pic">
      <img class="avatar" src="data:image/jpeg;base64,<?= base64_encode($com['dono_foto']) ?>" alt="Foto do dono">
    </div>

    <div style="margin-top:10px;text-align:center;">
      <div style="font-weight:700;color:var(--marrom);"><?= htmlspecialchars($com['dono_nome']) ?></div>
      <div style="font-size:13px;color:#7b6a58; margin-top:6px;">Dono da comunidade</div>
    </div>

    <div class="menu" style="width:100%; margin-top:14px;">
      <a href="forum.php?id=<?= $id_comunidade ?>">
        <!-- ícone simples fórum -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="12" rx="2" stroke="#6b4f36" stroke-width="1.2"/><path d="M7 8h10" stroke="#6b4f36" stroke-width="1.2" stroke-linecap="round"/></svg>
        Fórum
      </a>
      <a href="membros_comunidade.php?id=<?= $id_comunidade ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4z" stroke="#6b4f36" stroke-width="1.2"/><path d="M4 20c0-2.2 3.6-4 8-4s8 1.8 8 4" stroke="#6b4f36" stroke-width="1.2"/></svg>
        Membros
      </a>
      <a href="#regras">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 4h12v4H6z" stroke="#6b4f36" stroke-width="1.2"/><path d="M6 10h12v10H6z" stroke="#6b4f36" stroke-width="1.2"/></svg>
        Regras
      </a>
      <a href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="#6b4f36" stroke-width="1.2"/></svg>
        Chat
      </a>
      <a href="comunidade.php" class="ghost" style="margin-top:8px;">Voltar</a>
    </div>

    <div style="margin-top:12px;text-align:center;">
      <?php if ($usuario_dono): ?>
        <a href="editar_comunidade.php?id_comunidade=<?= $id_comunidade ?>" class="btn" style="display:inline-block;margin-top:8px;">Editar</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- CENTER: conteúdo principal -->
  <div class="center card">
    <div class="community-title">
      <div style="flex:1">
        <h1><?= htmlspecialchars($com['nome']) ?></h1>
        <div class="subtitle"><?= htmlspecialchars($com['descricao']) ?></div>
      </div>
      <div style="text-align:right;">
        <div style="font-size:13px;color:#7b6a58;">Criada em: <?= htmlspecialchars(date('d M Y', strtotime($com['criada_em'] ?? date('Y-m-d')))) ?></div>
      </div>
    </div>

    <!-- grande imagem da comunidade (layout como na foto) -->
    <?php if (!empty($com['imagem'])): ?>
      <img src="data:image/jpeg;base64,<?= base64_encode($com['imagem']) ?>" class="community-img" alt="<?= htmlspecialchars($com['nome']) ?>">
    <?php endif; ?>

    <!-- pequena "tabela" de informações estilo orkut -->
    <table class="info-table" role="table" aria-label="Informações da comunidade">
      <tr><td style="width:40%; font-weight:700; color:var(--marrom);">descrição:</td><td><?= nl2br(htmlspecialchars($com['descricao'])) ?></td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">idioma:</td><td>Português</td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">categoria:</td><td><?= htmlspecialchars($com['categoria_nome'] ?? 'Outros') ?></td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">dono:</td><td><?= htmlspecialchars($com['dono_nome']) ?></td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">moderadores:</td><td><?= htmlspecialchars($moderadores_nomes) ?></td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">tipo:</td><td><?= htmlspecialchars($com['tipo'] ?? 'pública') ?></td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">local:</td><td>Brasil</td></tr>
      <tr><td style="font-weight:700; color:var(--marrom);">membros:</td><td><?= $quantidade_usuarios ?></td></tr>
    </table>

    <!-- regras -->
    <?php if (!empty($regras)): ?>
      <div id="regras" class="rules">
        <strong>Regras da comunidade:</strong>
        <ul style="margin-top:8px;">
          <?php foreach($regras as $r): ?>
            <li><?= htmlspecialchars($r) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- ações (entrar/chat/sair) -->
    <div class="actions">
      <?php if (!$ja_membro): ?>
        <form method="POST" style="display:inline-block;margin:0;">
          <input type="hidden" name="acao" value="entrar">
          <button class="btn" type="submit">Participar da Comunidade</button>
        </form>
      <?php else: ?>
        <a class="btn alt" href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">Ir para o Chat</a>
        <?php if (!$usuario_dono): ?>
          <form method="POST" style="display:inline-block;margin:0;">
            <input type="hidden" name="acao" value="sair">
            <button class="btn ghost" type="submit" style="border-color:#d9d0bf;color:var(--marrom);">Sair da Comunidade</button>
          </form>
        <?php else: ?>
          <span class="btn" style="background:linear-gradient(180deg,#8b6a4f,#5a4224);">Você é o dono</span>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="small-note">Use os links à esquerda para navegar (Fórum, Membros, Regras, Chat).</div>
  </div>

  <!-- RIGHT: mini membros (igual à imagem) -->
  <div class="right card">
    <div class="members-title">membros (<?= $quantidade_usuarios ?>)</div>
    <div class="members-grid">
      <?php if (!empty($membros)): ?>
        <?php foreach($membros as $m): ?>
          <div class="member-box">
            <img src="data:image/jpeg;base64,<?= base64_encode($m['foto_de_perfil']) ?>" alt="<?= htmlspecialchars($m['nome']) ?>">
            <div class="member-name"><?= htmlspecialchars($m['nome']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="color:#7b6a58; font-size:13px;">Ainda não há membros visíveis.</div>
      <?php endif; ?>
    </div>

    <?php if ($quantidade_usuarios > count($membros)): ?>
      <div style="margin-top:10px;text-align:center;">
        <a href="membros_comunidade.php?id=<?= $id_comunidade ?>" class="btn" style="padding:6px 10px;font-size:13px;">ver todos</a>
      </div>
    <?php endif; ?>
  </div>

</div> <!-- /wrap -->

</body>
</html>
