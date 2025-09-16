<?php
session_start();
$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil-vendedor']) ? $_SESSION['foto_de_perfil-vendedor'] : null;

if (!isset($_SESSION['nome_vendedor'])) {
  header('Location: ../login/loginVendedor.php');
  exit;
}

// puxa categorias do banco
include '../conexao.php';
$categorias = [];
$stmt = $conn->query("SELECT * FROM categoria");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de livro - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
      --card-bg: #fff;
      --card-border: #ddd;
      --text-dark: #333;
      --text-muted: #666;
      --input-bg: #F4F1EE; /* Nova cor para os inputs */
    }

    * {
      margin: 0; 
      padding: 0; 
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: var(--background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding-left: 250px; /* espaço pro conteúdo não invadir a sidebar */
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 20px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
    } 

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background-color: #ccc;
      border-radius: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar .logo {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      width: 100%;
      padding: 0 20px;
      margin-bottom: 20px;
    }

    .sidebar .logo img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .sidebar .user-info {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .sidebar .user-info .nome-usuario {
      font-weight: bold;
      font-size: 0.95rem; 
      color: #fff;
    }

    .sidebar .user-info .tipo-usuario {
      font-size: 0.8rem;
      color: #ddd;
    }

    .sidebar nav {
      width: 100%;
      padding: 0 20px;
    }

    .sidebar nav h3 {
      margin-top: 20px;
      margin-bottom: 10px;
      font-size: 1rem;
      color: #ddd;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0;
      margin: 0 0 10px 0;
      width: 100%;
    }

    .sidebar nav ul li {
      width: 100%;
      margin-bottom: 10px;
    }

    .sidebar nav ul li a {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .sidebar nav ul li a i {
      margin-right: 10px;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    .topbar {
      position: fixed;
      top: 0; left: 250px; right: 0;
      height: 70px;
      background-color: var(--marrom);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      z-index: 1001;
    }

    .topbar h1 {
      font-size: 1.5rem;
    }

    .topbar input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 20px;
      width: 250px;
    }

    .content-area {
      margin-left: 100px;
      width: calc(100% - 250px);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 40px 20px;
      flex-direction: column;
    }

    main.conteudo {
      width: 100%;
      max-width: 700px;
      background-color: var(--card-bg); /* Fundo branco para o formulário */
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 40px 30px;
    }

    main.conteudo h1 {
      margin-bottom: 25px;
      text-align: center;
      color: var(--verde);
      font-weight: 700;
    }

    form {
      width: 100%;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 18px;
    }

    .form-group label {
      width: 180px;
      font-weight: 600;
      color: var(--marrom);
      text-align: right;
      margin-right: 15px;
      user-select: none;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="date"],
    .form-group textarea,
    .form-group input[type="file"],
    .form-group select {
      flex-grow: 1;
      padding: 8px 12px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: inherit;
      transition: border-color 0.3s ease;
      background-color: var(--input-bg); /* Cor de fundo igual ao background da página */
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--verde);
      box-shadow: 0 0 5px var(--verde);
    }

    /* Estilização específica para selects */
    .form-group select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235a4224' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 16px;
      padding-right: 35px;
    }

    /* Estilização do input file com fonte correta */
    input[type="file"] {
      font-family: 'Playfair Display', serif;
      color: var(--text-dark);
    }

    input[type="file"]::file-selector-button {
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      background-color: var(--verde);
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      margin-right: 12px;
      font-family: 'Playfair Display', serif;
    }

    input[type="file"]::file-selector-button:hover {
      background-color: #48603b;
      transform: translateY(-1px);
    }

    input[type="file"]::file-selector-button:active {
      background-color: #3b4e2f;
      transform: scale(0.98);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 14px;
      background-color: var(--verde);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1.15rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      margin-top: 20px;
      font-family: 'Playfair Display', serif;
    }

    input[type="submit"]:hover {
      background-color: #48603b;
      transform: translateY(-2px);
    }

    input[type="submit"]:active {
      background-color: #3b4e2f;
      transform: scale(0.98);
    }

    /* Estilo para a seção de detalhes de livro usado */
    #detalhes-usado {
      border-left: 4px solid var(--verde);
      padding-left: 15px;
      margin-left: 195px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    #detalhes-usado .form-group {
      margin-bottom: 12px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .content-area {
        margin-left: 200px;
        width: calc(100% - 200px);
      }
      
      #detalhes-usado {
        margin-left: 0;
        border-left: none;
        border-top: 4px solid var(--verde);
        padding-left: 0;
        padding-top: 15px;
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .content-area {
        margin-left: 0;
        width: 100%;
      }

      .form-group {
        flex-direction: column;
        align-items: flex-start;
      }

      .form-group label {
        width: 100%;
        text-align: left;
        margin-bottom: 6px;
      }
      
      #detalhes-usado {
        margin-left: 0;
        border-left: none;
        border-top: 4px solid var(--verde);
        padding-left: 0;
        padding-top: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
        <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>">
        <?php else: ?>
          <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
        <?php endif; ?>
        <div class="user-info">
          <p class="nome-usuario"><?= $nome_vendedor ? htmlspecialchars($nome_vendedor) : 'Entre ou crie sua conta'; ?></p>
        </div>
    </div>

    <nav>
      <ul class="menu">
        <li><a href="../painel-livreiro/painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="../painel-livreiro/anuncios.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Vendas publicadas</a></li>
        <li><a href="../painel-livreiro/rendimento.php"><img src="../../imgs/explorar.png.png" alt="Rendimento" style="width:20px; margin-right:10px;"> Rendimento</a></li>
        <li><a href="cadastroProduto.php"><img src="../../imgs/explorar.png.png" alt="Cadastro" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome_vendedor): ?>
          <li><a href="php/login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
          <li><a href="php/login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
        <?php else: ?>
          <li><a href="../painel-livreiro/minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Editar informações</a></li>
          <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="content-area">
    <main class="conteudo">
        <h1>Cadastre seu livro para venda:</h1>
        <form action="../insercao/insercaoProduto.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Título:</label>
            <input type="text" id="nome" name="nome" required>
        </div>

        <div class="form-group">
            <label for="autor">Autor:</label>
            <input type="text" id="autor" name="autor" required>
        </div>

        <div class="form-group">
            <label for="descricao">Sinopse:</label>
            <textarea id="descricao" name="descricao" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="editora">Editora:</label>
            <input type="text" id="editora" name="editora" required>
        </div>

        <div class="form-group">
            <label for="numero_paginas">Número de páginas:</label>
            <input type="number" id="numero_paginas" name="numero_paginas" min="1" required>
        </div>

        <div class="form-group">
            <label for="classificacao_idade">Classificação etária:</label>
            <input type="number" id="classificacao_idade" name="classificacao_idade" min="0" required>
        </div>

        <div class="form-group">
            <label for="data_publicacao">Data de publicação:</label>
            <input type="date" id="data_publicacao" name="data_publicacao" required>
        </div>

        <div class="form-group">
            <label for="idioma">Idioma:</label>
            <input type="text" id="idioma" name="idioma" required>
        </div>

        <div class="form-group">
          <label for="categoria">Categoria:</label>
          <select name="idcategoria" id="categoria" required>
            <option value="">Selecione a categoria</option>
              <?php foreach($categorias as $categoria): ?>
                <option value="<?= $categoria['idcategoria'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
              <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
            <label for="preco">Preço de venda (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" min="0" required>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-group">
            <label for="dimensoes">Dimensões:</label>
            <input type="text" id="dimensoes" name="dimensoes" required>
        </div>

        <div class="form-group">
            <label for="estado_livro">Estado do livro:</label>
            <select name="estado_livro" id="estado_livro" required>
              <option value="novo">Novo</option>
              <option value="usado">Usado</option>
            </select>
        </div>

        <div id="detalhes-usado" style="display:none;">
          <div class="form-group">
              <label for="paginas_faltando">Tem páginas faltando?</label>
              <select id="paginas_faltando" name="paginas_faltando">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>

          <div class="form-group">
              <label for="paginas_rasgadas">Possui páginas rasgadas?</label>
              <select id="paginas_rasgadas" name="paginas_rasgadas">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>

          <div class="form-group">
              <label for="folhas_amareladas">As páginas estão amareladas?</label>
              <select id="folhas_amareladas" name="folhas_amareladas">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>

          <div class="form-group">
              <label for="anotacoes">Possui anotações/rabiscos?</label>
              <select id="anotacoes" name="anotacoes">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>

          <div class="form-group">
              <label for="lombada_danificada">A lombada está danificada?</label>
              <select id="lombada_danificada" name="lombada_danificada">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>

          <div class="form-group">
              <label for="capa_danificada">A capa está danificada?</label>
              <select id="capa_danificada" name="capa_danificada">
                  <option value="">Selecione</option>
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
              </select>
          </div>
      </div>

        <div class="form-group">
            <label for="estado_detalhado">Descrição do estado:</label>
            <textarea id="estado_detalhado" name="estado_detalhado" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem do produto:</label>
            <input type="file" id="imagem" name="imagens[]" accept="image/*" multiple required>
        </div>

        <input type="submit" value="Cadastrar">
        </form>
    </main>
  </div>
  <script>
    document.getElementsByName('estado_livro')[0].addEventListener('change', function() {
        var usadoDiv = document.getElementById('detalhes-usado');
        if(this.value === 'usado') {
            usadoDiv.style.display = 'block';
            // Se quiser tornar os campos obrigatórios somente quando 'usado':
            usadoDiv.querySelectorAll('select').forEach(function(el) {
                el.required = true;
            });
        } else {
            usadoDiv.style.display = 'none';
            usadoDiv.querySelectorAll('select').forEach(function(el) {
                el.required = false;
            });
        }
    });
  </script>
</body>
</html>