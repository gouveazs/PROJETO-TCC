<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Crie sua conta - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #F4F1EE;
      width: 800px;
      height: 500px;
      border-radius: 20px;
      display: flex;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .left-panel {
      background-color: #5a6b50;
      color: white;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .left-panel h2 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    .left-panel p {
      font-size: 16px;
      margin-bottom: 20px;
      text-align: center;
    }

    .left-panel button {
      padding: 10px 30px;
      border: 2px solid white;
      background-color: transparent;
      color: white;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      transition: 0.3s;
    }

    .left-panel button:hover {
      background-color: white;
      color: #5a6b50;
    }

    .right-panel {
      flex: 1;
      background-color: #ffffff;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
    }

    .right-panel h2 {
      color: #5a6b50;
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="password"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    /* ====== INPUT DE ARQUIVO ESTILIZADO ====== */
    .custom-file {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .custom-file input[type="file"] {
      display: none; /* Esconde o input padrão */
    }

    .custom-file label {
      display: block;
      width: 100%;
      padding: 12px;
      background-color: #5a6b50;
      color: white;
      text-align: center;
      border-radius: 20px;
      cursor: pointer;
      transition: 0.3s;
      font-size: 14px;
    }

    .custom-file label:hover {
      background-color: #3f4e39;
    }

    .file-name {
      margin-top: 5px;
      font-size: 13px;
      color: #555;
      text-align: center;
    }

    form input[type="submit"] {
      padding: 12px;
      background-color: #5a6b50;
      color: white;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    form input[type="submit"]:hover {
      background-color: #3f4e39;
    }

    .erro {
      color: red;
      margin-top: 10px;
      text-align: center;
    }

    /* Modal para upload de foto */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .modal-header h2 {
      color: #333;
      font-size: 24px;
    }

    .close-modal {
      font-size: 28px;
      cursor: pointer;
      color: #999;
    }

    .close-modal:hover {
      color: #333;
    }

    .upload-area {
      border: 2px dashed #ddd;
      border-radius: 8px;
      padding: 40px 20px;
      text-align: center;
      margin-bottom: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .upload-area:hover {
      border-color: #5a6b50;
      background-color: #f9f9f9;
    }

    .upload-area p {
      color: #666;
      margin-bottom: 15px;
    }

    .btn-upload {
      background-color: #5a6b50;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    .btn-upload:hover {
      background-color: #4a5a40;
    }

    .preview-container {
      text-align: center;
      margin-top: 20px;
      display: none;
    }

    .preview-img {
      max-width: 200px;
      max-height: 200px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .btn-confirmar {
      background-color: #5a6b50;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
      margin-top: 10px;
    }

    .btn-confirmar:hover {
      background-color: #4a5a40;
    }

    .btn-confirmar:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <h2>Bem-vindo de volta!</h2>
      <p>Para se manter conectado,<br>faça login com suas informações pessoais</p>
      <button onclick="window.location.href='../login/login.php'">ENTRAR</button>
    </div>
    <div class="right-panel">
      <h2>Criar Conta</h2>
      <form method="POST" action="../insercao/insercao.php" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome de usuário" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>

        <!-- INPUT DE FOTO DE PERFIL -->
        <div class="custom-file">
          <input type="file" id="foto" name="foto_de_perfil" accept="image/*" required>
          <label for="foto" id="fotoLabel">Adicione foto de perfil</label>
          <div class="file-name" id="file-name">Nenhum arquivo escolhido</div>
        </div>

        <input type="submit" value="Cadastrar">
      </form>
      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'nome_ou_email'): ?>
        <div class="erro">Nome ou e-mail já cadastrados!</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal para upload de foto -->
  <div class="modal" id="modalUpload">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Alterar Foto de Perfil</h2>
        <span class="close-modal" id="closeModal">&times;</span>
      </div>
      <div class="upload-area" id="uploadArea">
        <p>Arraste uma imagem aqui ou clique para selecionar</p>
        <input type="file" id="fileInput" accept="image/*" style="display: none;">
        <button type="button" class="btn-upload" id="btnSelecionar">Selecionar Arquivo</button>
      </div>
      <div class="preview-container" id="previewContainer">
        <img id="previewImg" class="preview-img" src="" alt="Pré-visualização">
        <button type="button" class="btn-confirmar" id="btnConfirmar">Confirma</button>
      </div>
    </div>
  </div>

  <script>
    // Elementos DOM
    const fotoInput = document.getElementById("foto");
    const fileName = document.getElementById("file-name");
    const fotoLabel = document.getElementById("fotoLabel");
    const modalUpload = document.getElementById('modalUpload');
    const closeModal = document.getElementById('closeModal');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const btnSelecionar = document.getElementById('btnSelecionar');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('previewImg');
    const btnConfirmar = document.getElementById('btnConfirmar');

    // Mostra o nome do arquivo selecionado
    fotoInput.addEventListener("change", () => {
      if (fotoInput.files.length > 0) {
        fileName.textContent = fotoInput.files[0].name;
      } else {
        fileName.textContent = "Nenhum arquivo escolhido";
      }
    });

    // Abrir modal ao clicar no botão de adicionar foto
    fotoLabel.addEventListener('click', (e) => {
      e.preventDefault();
      abrirModal();
    });

    // Fechar modal
    closeModal.addEventListener('click', fecharModal);
    window.addEventListener('click', (event) => {
      if (event.target === modalUpload) {
        fecharModal();
      }
    });

    // Selecionar arquivo
    btnSelecionar.addEventListener('click', () => fileInput.click());
    uploadArea.addEventListener('click', () => fileInput.click());

    // Arrastar e soltar arquivo
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = '#5a6b50';
      uploadArea.style.backgroundColor = '#f9f9f9';
    });

    uploadArea.addEventListener('dragleave', () => {
      uploadArea.style.borderColor = '#ddd';
      uploadArea.style.backgroundColor = 'transparent';
    });

    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = '#ddd';
      uploadArea.style.backgroundColor = 'transparent';
      
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        processarImagem(e.dataTransfer.files[0]);
      }
    });

    // Processar imagem selecionada
    fileInput.addEventListener('change', (e) => {
      if (e.target.files.length) {
        processarImagem(e.target.files[0]);
      }
    });

    // Confirmar alteração da foto
    btnConfirmar.addEventListener('click', confirmarAlteracao);

    // Funções
    function abrirModal() {
      modalUpload.style.display = 'flex';
      previewContainer.style.display = 'none';
      fileInput.value = '';
    }

    function fecharModal() {
      modalUpload.style.display = 'none';
    }

    function processarImagem(file) {
      // Verificar se é uma imagem
      if (!file.type.match('image.*')) {
        alert('Por favor, selecione apenas arquivos de imagem.');
        return;
      }

      // Verificar tamanho do arquivo (máximo 5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('A imagem deve ter no máximo 5MB.');
        return;
      }

      // Criar pré-visualização
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        previewContainer.style.display = 'block';
        btnConfirmar.disabled = false;
      };
      reader.readAsDataURL(file);
    }

    function confirmarAlteracao() {
      // Atualizar o input de arquivo com a nova imagem
      fotoInput.files = fileInput.files;
      
      // Atualizar o nome do arquivo exibido
      if (fotoInput.files.length > 0) {
        fileName.textContent = fotoInput.files[0].name;
      }
      
      fecharModal();
    }
  </script>
</body>
</html>