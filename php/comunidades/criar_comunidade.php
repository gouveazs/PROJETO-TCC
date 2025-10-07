<?php
session_start();
include '../conexao.php';

$idusuario = $_SESSION['idusuario'] ?? null;
$nome_usuario = $_SESSION['nome_usuario'] ?? null;

if (!$idusuario) {
    header("Location: ../login/login.php");
}

$categorias = [];
$stmt = $conn->query("SELECT * FROM categoria");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $idcategoria = (int)$_POST['idcategoria'];
    $regras = trim($_POST['regras'] ?? '');

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    $sql = "INSERT INTO comunidades (nome, descricao, imagem, idusuario, idcategoria, status)
            VALUES (:nome, :descricao, :imagem, :idusuario, :idcategoria, 'ativa')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
    $stmt->bindParam(':idusuario', $idusuario);
    $stmt->bindParam(':idcategoria', $idcategoria);
    $stmt->execute();

    $idcomunidade = $conn->lastInsertId();

    $stmt = $conn->prepare("
        INSERT INTO membros_comunidade (idcomunidades, idusuario, papel)
        VALUES (:idcomunidade, :idusuario, 'dono')
    ");
    $stmt->execute([':idcomunidade' => $idcomunidade, ':idusuario' => $idusuario]);

    if (!empty($regras)) {
        $regras_array = explode("\n", $regras);
        $stmtRegras = $conn->prepare("
            INSERT INTO regras_comunidade (idcomunidades, regra)
            VALUES (:idcomunidade, :regra)
        ");
        foreach ($regras_array as $r) {
            $r = trim($r);
            if ($r !== '') {
                $stmtRegras->execute([':idcomunidade' => $idcomunidade, ':regra' => $r]);
            }
        }
    }

    header("Location: ver_comunidade.php?id=$idcomunidade");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Criar Comunidade - Entre Linhas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Playfair Display -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
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

/* Aumentar espaçamento entre label e campo */
.form-control label {
  margin-bottom: 10px;
  font-weight: 500;
  color: #333;
}

/* Aumentar altura dos campos (nome, select etc) */
.form-control input,
.form-control textarea,
.form-control select {
  padding: 14px 16px; /* maior padding interno */
  font-size: 15px;
}

/* Força os campos a ocuparem toda a largura */
.form-control-full {
  flex: 1 1 100%;
  display: flex;
  flex-direction: column;
}

.form-control {
  flex: 1 1 100%; /* <<< era 45%, agora ocupa 100% */
  display: flex;
  flex-direction: column;
}


#nome {
  margin-top: 5px;
  padding: 7px;
  border-radius: 5px !important;
  border: 1px solid #333; /* borda fininha */
  outline: none; /* remove a borda azul padrão */
}

#nome:focus {
  border: 1px solid #4f6443; /* mantém fininha ao focar */
  box-shadow: none; /* remove qualquer sombra extra */
  outline: none;
}

#descricao {
  margin-top: 5px;
  border-radius: 5px !important;
  border: 1px solid #333; /* borda fininha */
  outline: none; /* remove a borda azul padrão */
  resize: vertical; /* só permite aumentar verticalmente */
  min-height: 100px;
  max-height: 300px; /* impede de sair do espaço */
  width: 100%; /* garante que não passe da largura */
  overflow: auto; /* adiciona barra de rolagem se o texto passar do limite */
}

#descricao:focus {
  border: 1px solid #4f6443; /* mantém fininha ao focar */
  box-shadow: none; /* remove qualquer sombra extra */
  outline: none;
}

/* aplica a mesma lógica a todos os textareas */
textarea {
  width: 100%;
  max-width: 100%;
  min-width: 100%;
  resize: both;
  overflow: auto;
  border: 1px solid #333;
  border-radius: 5px;
  outline: none;
}

textarea:focus {
  border: 1px solid #4f6443;
  box-shadow: none;
  outline: none;
}


#regras {
  margin-top: 5px;
  border-radius: 5px !important;
  border: 1px solid #333; /* borda fininha */
  outline: none; /* remove a borda azul padrão */
  resize: vertical; /* só permite aumentar verticalmente */
  min-height: 100px;
  max-height: 300px; /* impede de sair do espaço */
  width: 100%; /* garante que não passe da largura */
  overflow: auto; /* adiciona barra de rolagem se o texto passar do limite */
}

#regras:focus {
  border: 1px solid #4f6443; /* mantém fininha ao focar */
  box-shadow: none; /* remove qualquer sombra extra */
  outline: none;
}


#idcategoria {
  margin-top: 5px;
  border-radius: 5px !important;
  padding: 7px;
  border: 1px solid #333; /* borda fininha */
  outline: none; /* remove a borda azul padrão */
}

#idcategoria:focus {
  border: 1px solid #4f6443; /* mantém fininha ao focar */
  box-shadow: none; /* remove qualquer sombra extra */
  outline: none;
}

.form-control input:focus,
.form-control textarea:focus,
.form-control select:focus {
  border-color: #5a6b50;
  outline: none;
}

.form-control textarea {
  resize: vertical;
  min-height: 100px;
}

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

.divider {
  height: 1px;
  background-color: #eee;
  margin: 25px 0;
  width: 100%;
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
    <div class="form-header">Criar Nova Comunidade</div>
    <form method="POST" enctype="multipart/form-data" id="communityForm">
      <div class="form-group">
        <div class="form-control-full">
          <label for="nome">Nome da Comunidade</label>
          <input type="text" id="nome" name="nome" required>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control-full">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao"></textarea>
        </div>
      </div>

      <!-- INPUT DE FOTO DE PERFIL -->
      <div class="form-group">
        <div class="form-control-full">
          <div class="custom-file">
            <input type="file" id="imagem" name="imagem" accept="image/*">
            <label for="imagem" id="fotoLabel">Adicione foto de perfil da comunidade</label>
            <div class="file-name" id="file-name">Nenhum arquivo escolhido</div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control-full">
          <label for="idcategoria">Categoria</label>
          <select id="idcategoria" name="idcategoria" required>
            <option value="">Selecione a categoria</option>
            <?php foreach($categorias as $c): ?>
              <option value="<?= $c['idcategoria'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control-full">
          <label for="regras">Regras iniciais (uma por linha, opcional)</label>
          <textarea id="regras" name="regras" placeholder="Ex: Respeitar os membros&#10;Proibido spam"></textarea>
        </div>
      </div>

      <div class="form-footer">
        <input type="submit" value="Cadastrar">
      </div>
    </form>
  </div>

  <!-- Modal para upload de foto -->
  <div class="modal" id="modalUpload">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Adicionar Foto da Comunidade</h2>
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
    const fotoInput = document.getElementById("imagem");
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

      const files = e.dataTransfer.files;
      if (files.length > 0 && files[0].type.startsWith('image/')) {
        fileInput.files = files;
        mostrarPreview(files[0]);
      }
    });

    // Mostrar preview da imagem selecionada
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        mostrarPreview(fileInput.files[0]);
      }
    });

    // Confirmar imagem no modal
    btnConfirmar.addEventListener('click', () => {
      const file = fileInput.files[0];
      if (!file) return;

      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      fotoInput.files = dataTransfer.files;
      fileName.textContent = file.name;

      fecharModal();
    });

    function mostrarPreview(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        previewContainer.style.display = 'block';
      };
      reader.readAsDataURL(file);
      btnConfirmar.disabled = false;
    }

    function abrirModal() {
      modalUpload.style.display = 'flex';
      previewContainer.style.display = 'none';
      fileInput.value = '';
      btnConfirmar.disabled = true;
    }

    function fecharModal() {
      modalUpload.style.display = 'none';
    }
  </script>
</body>
</html>
