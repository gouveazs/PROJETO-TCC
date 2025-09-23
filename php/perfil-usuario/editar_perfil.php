<?php
session_start();
include '../conexao.php';

$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

if (!$nome_usuario) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE nome = ?");
$stmt->execute([$nome_usuario]);
$dados_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$idusuario = isset($dados_usuario['idusuario']) ? $dados_usuario['idusuario'] : null;

if (!$idusuario) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_completo = $_POST['nome_completo'];
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto_de_perfil = file_get_contents($_FILES['foto']['tmp_name']);
        $stmt = $conn->prepare(
            "UPDATE usuario SET nome_completo = ?, nome = ?, email = ?, cpf = ?, cep = ?, cidade = ?, estado = ?, rua = ?, bairro = ?, foto_de_perfil = ? WHERE idusuario = ?"
        );
        $stmt->execute([$nome_completo, $novo_nome, $novo_email, $cpf, $cep, $cidade, $estado, $rua, $bairro, $foto_de_perfil, $idusuario]);
    } else {
        $stmt = $conn->prepare(
            "UPDATE usuario SET nome_completo = ?, nome = ?, email = ?, cpf = ?, cep = ?, cidade = ?, estado = ?, rua = ?, bairro = ? WHERE idusuario = ?"
        );
        $stmt->execute([$nome_completo, $novo_nome, $novo_email, $cpf, $cep, $cidade, $estado, $rua, $bairro, $idusuario]);
    }

    $_SESSION['nome_usuario'] = $novo_nome;
    $_SESSION['foto_de_perfil'] = $foto_de_perfil;
    header("Location: ver_perfil.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Formatar dados para exibição
$foto_perfil_src = !empty($usuario['foto_de_perfil']) ? 
    'data:image/jpeg;base64,' . base64_encode($usuario['foto_de_perfil']) : 
    'https://via.placeholder.com/140';

// Formatar CPF se existir
$cpf_formatado = '';
if (!empty($usuario['cpf'])) {
    $cpf_limpo = preg_replace('/\D/', '', $usuario['cpf']);
    if (strlen($cpf_limpo) === 11) {
        $cpf_formatado = substr($cpf_limpo, 0, 3) . '.' . 
                         substr($cpf_limpo, 3, 3) . '.' . 
                         substr($cpf_limpo, 6, 3) . '-' . 
                         substr($cpf_limpo, 9, 2);
    }
}

// Calcular ano de cadastro (exemplo)
$ano_cadastro = date('Y', strtotime($usuario['data_cadastro'] ?? 'now'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Perfil - Entre Linhas</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .perfil-container {
      background: #ffffff;
      padding: 40px;
      width: 100%;
      max-width: 900px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      display: flex;
      gap: 40px;
    }

    .perfil-lateral {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding-right: 30px;
      border-right: 1px solid #eaeaea;
    }

    .perfil-foto-container {
      position: relative;
      margin-bottom: 25px;
    }

    .perfil-foto {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #eaeaea;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .perfil-foto:hover {
      opacity: 0.8;
    }

    .foto-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      cursor: pointer;
    }

    .foto-overlay:hover {
      opacity: 1;
    }

    .foto-overlay span {
      color: white;
      font-size: 14px;
      text-align: center;
    }

    .perfil-principal {
      flex: 2;
    }

    h1 {
      font-size: 32px;
      color: #333;
      margin-bottom: 5px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .user-role {
      color: #666;
      font-size: 14px;
      margin-bottom: 30px;
      font-weight: 400;
      font-style: italic;
    }

    .info-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    .info-group {
      margin-bottom: 30px;
    }

    .info-group h3 {
      font-size: 18px;
      color: #5a6b50;
      margin-bottom: 20px;
      font-weight: 600;
      padding-bottom: 8px;
      border-bottom: 2px solid #eaeaea;
    }

    .info-linha {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
    }

    .info-linha:last-child {
      margin-bottom: 0;
    }

    .info-linha strong {
      color: #555;
      font-weight: 500;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .info-linha span {
      font-size: 16px;
      color: #333;
      font-weight: 400;
      padding: 6px 0;
    }

    .info-linha input {
      width: 100%;
      font-size: 16px;
      color: #333;
      border: 1px solid #ddd;
      background: #fff;
      outline: none;
      padding: 8px 12px;
      font-weight: 400;
      border-radius: 6px;
      transition: border-color 0.3s ease;
    }

    .info-linha input:focus {
      border-color: #5a6b50;
    }

    .info-linha input.empty-field {
      color: #999;
      font-style: italic;
    }

    .btn-container {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 30px;
      width: 100%;
    }

    .btn-normie,
    .btn-sair,
    .btn-voltar,
    .btn-salvar {
      padding: 14px;
      font-size: 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 500;
      width: 100%;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .btn-normie {
      background-color: #5a6b50;
      color: #fff;
    }

    .btn-normie:hover {
      background-color: #4a5a40;
    }

    .btn-sair {
      background-color: #c44;
      color: #fff;
    }

    .btn-sair:hover {
      background-color: #b33;
    }

    .btn-voltar {
      background-color: #f0f0f0;
      color: #666;
      border: 1px solid #ddd;
    }

    .btn-voltar:hover {
      background-color: #e5e5e5;
    }

    .btn-salvar {
      background-color: #5a6b50;
      color: #fff;
      margin-top: 20px;
    }

    .btn-salvar:hover {
      background-color: #4a5a40;
    }

    .btn-icon {
      margin-right: 10px;
      width: 16px;
      height: 16px;
    }

    .empty-field {
      color: #999 !important;
      font-style: italic;
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

    @media (max-width: 768px) {
      .perfil-container {
        flex-direction: column;
        gap: 30px;
        padding: 30px;
      }
      
      .perfil-lateral {
        padding-right: 0;
        border-right: none;
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 30px;
      }
      
      .info-container {
        grid-template-columns: 1fr;
        gap: 20px;
      }
    }

    @media (max-width: 480px) {
      .perfil-container {
        padding: 25px 20px;
      }
      
      h1 {
        font-size: 26px;
      }
      
      .perfil-foto {
        width: 120px;
        height: 120px;
      }
      
      .modal-content {
        padding: 20px;
      }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <form method="POST" enctype="multipart/form-data" id="formPerfil">
    <div class="perfil-container">
      <div class="perfil-lateral">
        <div class="perfil-foto-container">
          <img src="<?= $foto_perfil_src ?>" alt="Foto do usuário" class="perfil-foto" id="fotoPerfil" />
          <div class="foto-overlay" id="fotoOverlay">
            <span>Alterar<br>Foto</span>
          </div>
          <input type="file" id="fotoInput" name="foto" accept="image/*" style="display: none;">
        </div>
        <h1><input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" style="border: none; background: transparent; text-align: center; font-size: 32px; color: #333; width: 100%; outline: none;"></h1>
        <div class="user-role">Membro desde <?= $ano_cadastro ?></div>
        
        <div class="btn-container">
          <button type="submit" class="btn-salvar">
            <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
              <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
            </svg>
            SALVAR ALTERAÇÕES
          </button>

          <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">
            <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
              <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
            </svg>
            DELETAR CONTA
          </a>

          <a href="../../index.php" class="btn-voltar">
            ⬅ VOLTAR AO INÍCIO
          </a>
        </div>
      </div>

      <div class="perfil-principal">
        <div class="info-container">
          <div class="info-group">
            <h3>INFORMAÇÕES PESSOAIS</h3>
            <div class="info-linha">
              <strong>Nome completo</strong>
              <input type="text" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo'] ?? '') ?>">
            </div>

            <div class="info-linha">
              <strong>Email</strong>
              <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
            </div>

            <div class="info-linha">
              <strong>Senha</strong>
              <input type="password" name="senha" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" placeholder="Deixe em branco para manter a senha atual" readonly>
            </div>

            <div class="info-linha">
              <strong>CPF</strong>
              <input type="text" name="cpf" id="cpf" value="<?= htmlspecialchars($cpf_formatado) ?>" placeholder="Digite seu CPF">
            </div>
          </div>

          <div class="info-group">
            <h3>ENDEREÇO</h3>
            <div class="info-linha">
              <strong>CEP</strong>
              <input type="text" name="cep" id="cep" value="<?= htmlspecialchars($usuario['cep'] ?? '') ?>" placeholder="Digite seu CEP">
            </div>

            <div class="info-linha">
              <strong>Estado</strong>
              <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($usuario['estado'] ?? '') ?>" readonly>
            </div>

            <div class="info-linha">
              <strong>Cidade</strong>
              <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($usuario['cidade'] ?? '') ?>" readonly>
            </div>

            <div class="info-linha">
              <strong>Bairro</strong>
              <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($usuario['bairro'] ?? '') ?>" readonly>
            </div>

            <div class="info-linha">
              <strong>Rua</strong>
              <input type="text" name="rua" id="rua" value="<?= htmlspecialchars($usuario['rua'] ?? '') ?>" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

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
        <button type="button" class="btn-confirmar" id="btnConfirmar">Confirmar Alteração</button>
      </div>
    </div>
  </div>

  <script>
    // Elementos DOM
    const fotoPerfil = document.getElementById('fotoPerfil');
    const fotoInput = document.getElementById('fotoInput');
    const fotoOverlay = document.getElementById('fotoOverlay');
    const modalUpload = document.getElementById('modalUpload');
    const closeModal = document.getElementById('closeModal');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const btnSelecionar = document.getElementById('btnSelecionar');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('previewImg');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const formPerfil = document.getElementById('formPerfil');
    const cepInput = document.getElementById('cep');

    // Abrir modal ao clicar na foto ou overlay
    fotoPerfil.addEventListener('click', abrirModal);
    fotoOverlay.addEventListener('click', abrirModal);

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

    // Buscar endereço pelo CEP
    cepInput.addEventListener('blur', buscarEnderecoPorCEP);

    // Formatação de CPF
    document.getElementById('cpf').addEventListener('input', formatarCPF);

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
      // Atualizar a imagem de perfil
      fotoPerfil.src = previewImg.src;
      // Atualizar o input de arquivo com a nova imagem
      fotoInput.files = fileInput.files;
      
      fecharModal();
    }

    function formatarCPF(e) {
      let cpf = e.target.value.replace(/\D/g, '');
      
      if (cpf.length > 11) {
        cpf = cpf.substring(0, 11);
      }
      
      if (cpf.length === 11) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
      } else if (cpf.length > 9) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})/, '$1.$2.$3-');
      } else if (cpf.length > 6) {
        cpf = cpf.replace(/(\d{3})(\d{3})/, '$1.$2.');
      } else if (cpf.length > 3) {
        cpf = cpf.replace(/(\d{3})/, '$1.');
      }
      
      e.target.value = cpf;
    }

    async function buscarEnderecoPorCEP() {
      const cep = cepInput.value.replace(/\D/g, '');
      
      if (cep.length === 8) {
        try {
          const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
          const data = await response.json();
          
          if (!data.erro) {
            document.getElementById('estado').value = data.uf;
            document.getElementById('cidade').value = data.localidade;
            document.getElementById('bairro').value = data.bairro;
            document.getElementById('rua').value = data.logradouro;
            
            // Remover a classe empty-field dos campos preenchidos
            ['estado', 'cidade', 'bairro', 'rua'].forEach(id => {
              const campo = document.getElementById(id);
              campo.classList.remove('empty-field');
              campo.style.color = '#333';
              campo.style.fontStyle = 'normal';
            });
          } else {
            alert('CEP não encontrado. Verifique o número digitado.');
          }
        } catch (error) {
          console.error('Erro ao buscar CEP:', error);
          alert('Erro ao buscar endereço. Tente novamente.');
        }
      }
    }

    // Formatar CPF ao carregar a página
    document.addEventListener('DOMContentLoaded', () => {
      const cpfInput = document.getElementById('cpf');
      let cpf = cpfInput.value.replace(/\D/g, '');
      if (cpf.length === 11 && cpf !== '') {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        cpfInput.value = cpf;
      }
    });
  </script>
</body>
</html>