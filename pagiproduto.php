<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalhes do Livro - 1984</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="sidebar.css"> <!-- separei o CSS da sidebar -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .book-page {
      display: flex;
      flex-direction: row;
      margin: 2rem;
    }

    .book-images {
      flex: 1;
      padding-right: 2rem;
    }

    .book-images img {
      width: 100%;
      border-radius: 10px;
    }

    .thumbnail-list {
      display: flex;
      gap: 10px;
      margin-top: 1rem;
    }

    .thumbnail-list img {
      width: 60px;
      height: 80px;
      object-fit: cover;
      border: 2px solid transparent;
      cursor: pointer;
    }

    .thumbnail-list img.active {
      border-color: #28a745;
    }

    .book-info {
      flex: 1.2;
      background-color: #f8f9fa;
      padding: 2rem;
      border-radius: 10px;
    }

    .tabs {
      display: flex;
      margin-top: 2rem;
      border-bottom: 2px solid #ccc;
    }

    .tabs button {
      background: none;
      border: none;
      font-weight: bold;
      padding: 0.5rem 1rem;
      cursor: pointer;
    }

    .tabs button.active {
      border-bottom: 3px solid #28a745;
    }

    .tab-content {
      padding: 1rem 0;
    }

    .condition-tabs {
      display: flex;
      justify-content: space-between;
      background-color: #e9ecef;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    .condition-tabs div {
      flex: 1;
      text-align: center;
      padding: 0.8rem;
      font-weight: bold;
      cursor: pointer;
    }

    .condition-tabs .active {
      background-color: white;
      border-bottom: 3px solid #28a745;
      
    }
  </style>
</head>
<body>
  <!-- SIDEBAR INCLUÍDA AQUI -->
  <?php include('sidebar.php'); ?>

  <div class="book-page">
    <div class="book-images">
      <img src="imgs/1984.jpg" id="mainImage" alt="Capa do Livro 1984">
      <div class="thumbnail-list">
        <img src="imgs/1984.jpg" class="active" onclick="switchImage(this)">
        <img src="imgs/1984.jpg" onclick="switchImage(this)">
        <img src="imgs/1984.jpg" onclick="switchImage(this)">
      </div>
    </div>

    <div class="book-info">
      <h2>1984</h2>
      <p><strong>Autor:</strong> George Orwell</p>
      <p><strong>Ano:</strong> 1949</p>
      <p><strong>ISBN:</strong> 9788535914849</p>

      <div class="condition-tabs">
        <div id="tab-usado" class="active" onclick="changeCondition('usado')">Usado</div>
        <div id="tab-novo" onclick="changeCondition('novo')">Novo</div>
      </div>

      <div id="usado-info">
        <p><strong>Preço:</strong> R$ 35,00</p>
        <p><strong>Descrição:</strong> Livro com leve desgaste nas bordas. Nenhuma página faltando.</p>
        <p><strong>Vendido por:</strong> Sebo Moderno</p>
      </div>

      <div id="novo-info" style="display: none;">
        <p><strong>Preço:</strong> R$ 52,00</p>
        <p><strong>Descrição:</strong> Livro novo, lacrado. Em estoque.</p>
        <p><strong>Vendido por:</strong> Livraria Moderna</p>
      </div>

      <button class="btn btn-success mt-3">Comprar</button>
    </div>
  </div>

  <div class="container">
    <div class="tabs">
      <button class="active" onclick="showTab('desc')">Descrição</button>
      <button onclick="showTab('info')">Informações Adicionais</button>
      <button onclick="showTab('reviews')">Avaliações</button>
    </div>

    <div id="desc" class="tab-content">
      <p>Em 1984, George Orwell constrói uma distopia assustadora, onde o Estado controla tudo e todos. Uma crítica forte ao totalitarismo e à vigilância extrema.</p>
    </div>
    <div id="info" class="tab-content" style="display:none">
      <ul>
        <li>Dimensões: 21 x 14 cm</li>
        <li>Peso: 350g</li>
        <li>Páginas: 328</li>
      </ul>
    </div>
    <div id="reviews" class="tab-content" style="display:none">
      <p>★★★★★ - Um clássico essencial! - Maria</p>
      <p>★★★★☆ - Muito bom, mas pesado. - João</p>
    </div>
  </div>

  <script>
    function switchImage(el) {
      document.getElementById("mainImage").src = el.src;
      document.querySelectorAll(".thumbnail-list img").forEach(img => img.classList.remove("active"));
      el.classList.add("active");
    }

    function changeCondition(condition) {
      document.getElementById("usado-info").style.display = condition === 'usado' ? 'block' : 'none';
      document.getElementById("novo-info").style.display = condition === 'novo' ? 'block' : 'none';

      document.getElementById("tab-usado").classList.remove("active");
      document.getElementById("tab-novo").classList.remove("active");
      document.getElementById(`tab-${condition}`).classList.add("active");
    }

    function showTab(tab) {
      document.querySelectorAll(".tab-content").forEach(tc => tc.style.display = "none");
      document.getElementById(tab).style.display = "block";

      document.querySelectorAll(".tabs button").forEach(btn => btn.classList.remove("active"));
      event.target.classList.add("active");
    }
  </script>
</body>
</html>
