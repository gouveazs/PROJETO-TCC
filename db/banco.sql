CREATE DATABASE IF NOT EXISTS banco CHARACTER SET utf8;
USE banco;

-- ========================================
-- Usuário
-- ========================================
CREATE TABLE usuario (
  idusuario INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(45),
  email VARCHAR(45),
  senha VARCHAR(455),
  nome_completo VARCHAR(145),
  telefone CHAR(14),
  cpf CHAR(11),
  cep CHAR(8),
  estado CHAR(2),
  cidade VARCHAR(45),
  bairro VARCHAR(45),
  rua VARCHAR(45),
  numero VARCHAR(45),
  foto_de_perfil LONGBLOB,
  status ENUM('ativo', 'desativado') DEFAULT 'ativo',
  PRIMARY KEY (idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Vendedor
-- ========================================
CREATE TABLE vendedor (
  idvendedor INT NOT NULL AUTO_INCREMENT,
  nome_completo VARCHAR(45),
  email VARCHAR(45) NOT NULL,
  senha VARCHAR(455),
  data_nascimento DATE,
  cpf CHAR(11),
  cnpj CHAR(14),
  foto_de_perfil LONGBLOB,
  reputacao DECIMAL(5,2) DEFAULT 35.00,
  status ENUM('ativo', 'desativado') DEFAULT 'ativo',
  cep CHAR(8),
  estado CHAR(2),
  cidade VARCHAR(45),
  bairro VARCHAR(45),
  rua VARCHAR(45),
  numero VARCHAR(45),
  PRIMARY KEY (idvendedor)
) ENGINE=InnoDB;

-- ========================================
-- Categoria
-- ========================================
CREATE TABLE categoria (
  idcategoria INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(450),
  PRIMARY KEY (idcategoria)
) ENGINE=InnoDB;

-- ========================================
-- Produto
-- ========================================
CREATE TABLE produto (
  idproduto INT NOT NULL AUTO_INCREMENT,
  idvendedor INT NOT NULL,
  idcategoria INT NOT NULL,
  nome VARCHAR(45),
  numero_paginas VARCHAR(45),
  editora VARCHAR(45),
  classificacao_etaria INT,
  data_publicacao DATE,
  preco FLOAT,
  quantidade INT,
  descricao TEXT,
  autor VARCHAR(45),
  isbn CHAR(13),
  dimensoes VARCHAR(50),
  idioma VARCHAR(45),
  estado_livro ENUM('Usado', 'Novo'),
  paginas_faltando ENUM('Sim','Não') DEFAULT 'Não',
  folhas_amareladas ENUM('Sim','Não') DEFAULT 'Não',
  paginas_rasgadas ENUM('Sim','Não') DEFAULT 'Não',
  anotacoes ENUM('Sim','Não') DEFAULT 'Não',
  lombada_danificada ENUM('Sim','Não') DEFAULT 'Não',
  capa_danificada ENUM('Sim','Não') DEFAULT 'Não',
  estado_detalhado TEXT,
  status ENUM('Disponivel','Vendido') DEFAULT 'Disponivel',
  PRIMARY KEY (idproduto),
  FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor),
  FOREIGN KEY (idcategoria) REFERENCES categoria(idcategoria)
) ENGINE=InnoDB;

-- ========================================
-- Comunidades
-- ========================================
CREATE TABLE comunidades (
  idcomunidades INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(45) NOT NULL,
  descricao TEXT,
  imagem LONGBLOB,
  criada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  quantidade_usuarios INT,
  idusuario INT NOT NULL,
  idcategoria INT NOT NULL,
  regras TEXT,
  status ENUM('ativa','desativada') DEFAULT 'ativa',
  PRIMARY KEY (idcomunidades),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  FOREIGN KEY (idcategoria) REFERENCES categoria(idcategoria)
) ENGINE=InnoDB;

-- ========================================
-- Membros da comunidade
-- ========================================
CREATE TABLE membros_comunidade (
  idmembros_comunidade INT NOT NULL AUTO_INCREMENT,
  entrou_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  idcomunidades INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idmembros_comunidade, idcomunidades),
  FOREIGN KEY (idcomunidades) REFERENCES comunidades(idcomunidades),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Mensagens da comunidade
-- ========================================
CREATE TABLE mensagens_chat (
  idmensagens_chat INT NOT NULL AUTO_INCREMENT,
  mensagem TEXT,
  enviada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  idcomunidades INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idmensagens_chat),
  FOREIGN KEY (idcomunidades) REFERENCES comunidades(idcomunidades),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Pedido
-- ========================================
CREATE TABLE pedido (
  idpedido INT NOT NULL AUTO_INCREMENT,
  valor_total FLOAT,
  data_pedido DATE,
  idusuario INT NOT NULL,
  status ENUM('pendente', 'concluido', 'cancelado'),
  PRIMARY KEY (idpedido),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Item do pedido
-- ========================================
CREATE TABLE item_pedido (
  iditem_pedido INT NOT NULL AUTO_INCREMENT,
  quantidade INT,
  idproduto INT NOT NULL,
  idpedido INT NOT NULL,
  PRIMARY KEY (iditem_pedido, idpedido),
  FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  FOREIGN KEY (idpedido) REFERENCES pedido(idpedido)
) ENGINE=InnoDB;

-- ========================================
-- Carrinho
-- ========================================
CREATE TABLE carrinho (
  idcarrinho INT NOT NULL AUTO_INCREMENT,
  preco_unitario DOUBLE(10,2),
  idproduto INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idcarrinho),
  FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Favoritos
-- ========================================
CREATE TABLE favoritos (
  idfavoritos INT NOT NULL AUTO_INCREMENT,
  idproduto INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idfavoritos),
  FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- ========================================
-- Avaliações
-- ========================================
CREATE TABLE avaliacoes (
  idavaliacoes INT NOT NULL AUTO_INCREMENT,
  nota INT,
  comentario TEXT,
  data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  PRIMARY KEY (idavaliacoes),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- ========================================
-- Notificações
-- ========================================
CREATE TABLE notificacoes (
  idnotificacoes INT NOT NULL AUTO_INCREMENT,
  mensagem TEXT,
  lida TINYINT,
  data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  tipo ENUM('avaliação','compra'),
  PRIMARY KEY (idnotificacoes),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- ========================================
-- Recuperação de senha
-- ========================================
CREATE TABLE recuperacao_senha (
  idrecuperacao_senha INT NOT NULL AUTO_INCREMENT,
  token VARCHAR(255) NOT NULL,
  expira_em DATETIME NOT NULL,
  usado TINYINT DEFAULT 0,
  idusuario INT,
  idvendedor INT,
  PRIMARY KEY (idrecuperacao_senha),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- ========================================
-- Conversa direta (usuário <-> vendedor)
-- ========================================
CREATE TABLE conversa (
  idconversa INT NOT NULL AUTO_INCREMENT,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('ativada', 'finalizada'),
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  PRIMARY KEY (idconversa),
  FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- ========================================
-- Mensagens diretas
-- ========================================
CREATE TABLE mensagens (
  idmensagens INT NOT NULL AUTO_INCREMENT,
  idconversa INT NOT NULL,
  remetente_tipo ENUM('usuario', 'vendedor'),
  remetente_id INT,
  conteudo TEXT,
  data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  lida TINYINT,
  PRIMARY KEY (idmensagens),
  FOREIGN KEY (idconversa) REFERENCES conversa(idconversa)
) ENGINE=InnoDB;

-- ========================================
-- Imagens adicionais de produtos
-- ========================================
CREATE TABLE imagens (
  idimagens INT NOT NULL AUTO_INCREMENT,
  imagem LONGBLOB,
  idproduto INT NOT NULL,
  PRIMARY KEY (idimagens),
  FOREIGN KEY (idproduto) REFERENCES produto(idproduto)
) ENGINE=InnoDB;