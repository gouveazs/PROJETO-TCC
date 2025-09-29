-- -----------------------------------------------------
-- Criar banco de dados
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS banco DEFAULT CHARACTER SET utf8;
USE banco;

-- -----------------------------------------------------
-- Tabela usuario
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
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
  status ENUM('ativo','desativado') DEFAULT 'ativo',
  PRIMARY KEY (idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela vendedor
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS vendedor (
  idvendedor INT NOT NULL AUTO_INCREMENT,
  nome_completo VARCHAR(45),
  email VARCHAR(45) NOT NULL,
  senha VARCHAR(455),
  data_nascimento DATE,
  cpf CHAR(11),
  cnpj CHAR(14),
  foto_de_perfil LONGBLOB,
  reputacao DECIMAL(5,2) DEFAULT 35.00,
  status ENUM('ativo','desativado') DEFAULT 'ativo',
  cep CHAR(8),
  estado CHAR(2),
  cidade VARCHAR(45),
  bairro VARCHAR(45),
  rua VARCHAR(45),
  numero VARCHAR(45),
  PRIMARY KEY (idvendedor)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela categoria
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS categoria (
  idcategoria INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(450),
  PRIMARY KEY (idcategoria)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela produto
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS produto (
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
  estado_livro ENUM('Usado','Novo'),
  paginas_faltando ENUM('Sim','Não') DEFAULT 'Não',
  folhas_amareladas ENUM('Sim','Não') DEFAULT 'Não',
  paginas_rasgadas ENUM('Sim','Não') DEFAULT 'Não',
  anotacoes ENUM('Sim','Não') DEFAULT 'Não',
  lombada_danificada ENUM('Sim','Não') DEFAULT 'Não',
  capa_danificada ENUM('Sim','Não') DEFAULT 'Não',
  estado_detalhado TEXT,
  status ENUM('Disponivel','Vendido') DEFAULT 'Disponivel',
  PRIMARY KEY (idproduto),
  INDEX fk_produto_cadastro_vendedor1_idx (idvendedor),
  INDEX fk_produto_categoria1_idx (idcategoria),
  CONSTRAINT fk_produto_cadastro_vendedor1 FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor),
  CONSTRAINT fk_produto_categoria1 FOREIGN KEY (idcategoria) REFERENCES categoria(idcategoria)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela pedido
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pedido (
  idpedido INT NOT NULL AUTO_INCREMENT,
  valor_total FLOAT,
  data_pedido DATE,
  idusuario INT NOT NULL,
  status ENUM('pendente','concluido','cancelado'),
  PRIMARY KEY (idpedido),
  INDEX fk_pedido_usuario1_idx (idusuario),
  CONSTRAINT fk_pedido_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela item_pedido
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS item_pedido (
  iditem_pedido INT NOT NULL AUTO_INCREMENT,
  quantidade INT,
  idproduto INT NOT NULL,
  idpedido INT NOT NULL,
  PRIMARY KEY (iditem_pedido, idpedido),
  INDEX fk_item_pedido_produto1_idx (idproduto),
  INDEX fk_item_pedido_pedido1_idx (idpedido),
  CONSTRAINT fk_item_pedido_produto1 FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  CONSTRAINT fk_item_pedido_pedido1 FOREIGN KEY (idpedido) REFERENCES pedido(idpedido)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela comunidades
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS comunidades (
  idcomunidades INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(45) NOT NULL,
  descricao TEXT,
  imagem LONGBLOB,
  criada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  quantidade_usuarios INT,
  idusuario INT NOT NULL,
  PRIMARY KEY (idcomunidades),
  INDEX fk_comunidades_usuario1_idx (idusuario),
  CONSTRAINT fk_comunidades_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela membros_comunidade
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS membros_comunidade (
  idmembros_comunidade INT NOT NULL AUTO_INCREMENT,
  entrou_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  idcomunidades INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idmembros_comunidade, idcomunidades),
  INDEX fk_membros_comunidade_comunidades1_idx (idcomunidades),
  INDEX fk_membros_comunidade_usuario1_idx (idusuario),
  CONSTRAINT fk_membros_comunidade_comunidades1 FOREIGN KEY (idcomunidades) REFERENCES comunidades(idcomunidades),
  CONSTRAINT fk_membros_comunidade_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela mensagens_chat
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS mensagens_chat (
  idmensagens_chat INT NOT NULL AUTO_INCREMENT,
  mensagem TEXT,
  enviada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  idcomunidades INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idmensagens_chat),
  INDEX fk_mensagens_chat_comunidades1_idx (idcomunidades),
  INDEX fk_mensagens_chat_usuario1_idx (idusuario),
  CONSTRAINT fk_mensagens_chat_comunidades1 FOREIGN KEY (idcomunidades) REFERENCES comunidades(idcomunidades),
  CONSTRAINT fk_mensagens_chat_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela imagens
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS imagens (
  idimagens INT NOT NULL AUTO_INCREMENT,
  imagem LONGBLOB,
  idproduto INT NOT NULL,
  PRIMARY KEY (idimagens),
  INDEX fk_imagens_produto1_idx (idproduto),
  CONSTRAINT fk_imagens_produto1 FOREIGN KEY (idproduto) REFERENCES produto(idproduto)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela carrinho
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS carrinho (
  idcarrinho INT NOT NULL AUTO_INCREMENT,
  preco_unitario DOUBLE(10,2),
  idproduto INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idcarrinho),
  INDEX fk_carrinho_produto1_idx (idproduto),
  INDEX fk_carrinho_usuario1_idx (idusuario),
  CONSTRAINT fk_carrinho_produto1 FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  CONSTRAINT fk_carrinho_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela favoritos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS favoritos (
  idfavoritos INT NOT NULL AUTO_INCREMENT,
  idproduto INT NOT NULL,
  idusuario INT NOT NULL,
  PRIMARY KEY (idfavoritos),
  INDEX fk_favoritos_produto1_idx (idproduto),
  INDEX fk_favoritos_usuario1_idx (idusuario),
  CONSTRAINT fk_favoritos_produto1 FOREIGN KEY (idproduto) REFERENCES produto(idproduto),
  CONSTRAINT fk_favoritos_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela avaliacoes
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS avaliacoes (
  idavaliacoes INT NOT NULL AUTO_INCREMENT,
  nota INT,
  comentario TEXT,
  data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  PRIMARY KEY (idavaliacoes),
  INDEX fk_avaliacoes_usuario1_idx (idusuario),
  INDEX fk_avaliacoes_vendedor1_idx (idvendedor),
  CONSTRAINT fk_avaliacoes_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  CONSTRAINT fk_avaliacoes_vendedor1 FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela notificacoes
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS notificacoes (
  idnotificacoes INT NOT NULL AUTO_INCREMENT,
  mensagem TEXT,
  lida TINYINT,
  data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  tipo ENUM('avaliação','compra'),
  PRIMARY KEY (idnotificacoes),
  INDEX fk_notificacoes_usuario1_idx (idusuario),
  INDEX fk_notificacoes_vendedor1_idx (idvendedor),
  CONSTRAINT fk_notificacoes_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  CONSTRAINT fk_notificacoes_vendedor1 FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela recuperacao_senha
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS recuperacao_senha (
  idrecuperacao_senha INT NOT NULL AUTO_INCREMENT,
  token VARCHAR(255) NOT NULL,
  expira_em DATETIME NOT NULL,
  usado TINYINT DEFAULT 0,
  idusuario INT,
  idvendedor INT,
  PRIMARY KEY (idrecuperacao_senha),
  INDEX fk_recuperacao_senha_usuario1_idx (idusuario),
  INDEX fk_recuperacao_senha_vendedor1_idx (idvendedor),
  CONSTRAINT fk_recuperacao_senha_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  CONSTRAINT fk_recuperacao_senha_vendedor1 FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela conversa
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS conversa (
  idconversa INT NOT NULL AUTO_INCREMENT,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('ativada','finalizada'),
  idusuario INT NOT NULL,
  idvendedor INT NOT NULL,
  PRIMARY KEY (idconversa),
  INDEX fk_conversas_usuario1_idx (idusuario),
  INDEX fk_conversas_vendedor1_idx (idvendedor),
  CONSTRAINT fk_conversas_usuario1 FOREIGN KEY (idusuario) REFERENCES usuario(idusuario),
  CONSTRAINT fk_conversas_vendedor1 FOREIGN KEY (idvendedor) REFERENCES vendedor(idvendedor)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela mensagens
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS mensagens (
  idmensagens INT NOT NULL AUTO_INCREMENT,
  idconversa INT NOT NULL,
  remetente_tipo ENUM('usuario','vendedor'),
  remetente_id INT,
  conteudo TEXT,
  data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  lida TINYINT,
  PRIMARY KEY (idmensagens),
  INDEX fk_mensagens_conversa1_idx (idconversa),
  CONSTRAINT fk_mensagens_conversa1 FOREIGN KEY (idconversa) REFERENCES conversa(idconversa)
) ENGINE=InnoDB;