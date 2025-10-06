<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Torne-se um vendedor hoje - Entre Linhas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Playfair Display -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Playfair Display', serif;
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background-color: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 700px;
      width: 100%;
    }

    .form-header {
      font-size: 22px;
      font-weight: 600;
      color: #5a6b50;
      margin-bottom: 30px;
      text-align: center;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-control {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
    }

    .form-control label {
      margin-bottom: 8px;
      font-weight: 500;
      color: #333;
    }

    .form-control input {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s ease;
      font-family: 'Playfair Display', serif;
    }

    .form-control input:focus {
      border-color: #5a6b50;
      outline: none;
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

    .form-footer {
      text-align: center;
      margin-top: 30px;
    }

    input[type="submit"] {
      background-color: #5a6b50;
      color: white;
      border: none;
      padding: 14px 28px;
      border-radius: 20px;
      font-weight: 600;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
      font-family: 'Playfair Display', serif;
      width: 100%;
    }

    input[type="submit"]:hover {
      background-color: #4a5843;
    }

    @media (max-width: 600px) {
      .form-group {
        flex-direction: column;
      }

      .form-control {
        flex: 1 1 100%;
      }
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
    <div class="form-header">Preencha com suas informações</div>
    <form method="POST" action="../insercao/insercaoVendedor.php" enctype="multipart/form-data">
      <div class="form-group">
        <div class="form-control">
          <label for="nomeV">Nome completo</label>
          <input type="text" id="nomeV" name="nomeV" required>
        </div>
        <div class="form-control">
          <label for="idade">Data de nascimento</label>
          <input type="date" id="data_nascimento" name="data_nascimento" required>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control">
          <label for="emailV">E-mail</label>
          <input type="email" id="emailV" name="emailV" required>
        </div>
        <div class="form-control">
          <label for="senhaV">Senha</label>
          <input type="password" id="senhaV" name="senhaV" required>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" required>
        </div>
        <div class="form-control">
          <label for="cnpj">CNPJ</label>
          <input type="text" id="cnpj" name="cnpj">
        </div>
      </div>

      <!-- INPUT DE FOTO DE PERFIL -->
      <div class="form-group">
        <div class="form-control">
          <div class="custom-file">
            <input type="file" id="foto" name="foto_de_perfil" accept="image/*" required>
            <label for="foto" id="fotoLabel">Adicione foto de perfil</label>
            <div class="file-name" id="file-name">Nenhum arquivo escolhido</div>
          </div>
        </div>
      </div>

      <div class="form-footer">
        <input type="submit" value="Cadastrar">
      </div>
    </form>
    <?php if (isset($_GET['erro']) && $_GET['erro'] == 'nome_ou_email'): ?>
      <div class="erro">Nome ou e-mail já cadastrados!</div>
    <?php endif; ?>
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
        <button type="button" class="btn-confirmar" id="btnConfirmar">Confirmar</button>
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
  <!-- VLibras - Widget de Libras -->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
</body>
</html>