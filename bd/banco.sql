-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema banco
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema banco
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `banco` DEFAULT CHARACTER SET utf8 ;
USE `banco` ;

-- -----------------------------------------------------
-- Table `banco`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`usuario` (
  `idusuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `nome_completo` VARCHAR(145) NULL,
  `telefone` CHAR(14) NULL,
  `cpf` VARCHAR(11) NULL,
  `cep` VARCHAR(8) NULL,
  `pais` VARCHAR(45) NULL,
  `estado` CHAR(2) NULL,
  `cidade` VARCHAR(45) NULL,
  `bairro` VARCHAR(45) NULL,
  `rua` VARCHAR(45) NULL,
  `foto_de_perfil` LONGBLOB NULL,
  `status` ENUM('ativo', 'desativado') NULL DEFAULT 'ativo',
  PRIMARY KEY (`idusuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`login_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`login_usuario` (
  `idlogin` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `usuario_idusuario` INT NOT NULL,
  PRIMARY KEY (`idlogin`),
  INDEX `fk_login_usuario_usuario1_idx` (`usuario_idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_login_usuario_usuario1`
    FOREIGN KEY (`usuario_idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`vendedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`vendedor` (
  `idvendedor` INT NOT NULL AUTO_INCREMENT,
  `nome_completo` VARCHAR(45) NULL,
  `email` VARCHAR(45) NOT NULL,
  `senha` VARCHAR(45) NULL,
  `data_nascimento` DATE NULL,
  `cpf` CHAR(11) NULL,
  `cnpj` CHAR(14) NULL,
  `foto_de_perfil` LONGBLOB NULL,
  `cep` CHAR(8) NULL,
  `reputacao` DECIMAL(5,2) NULL DEFAULT 35.00,
  `status` ENUM('ativo', 'desativado') NULL DEFAULT 'ativo',
  PRIMARY KEY (`idvendedor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`categoria` (
  `idcategoria` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(450) NULL,
  PRIMARY KEY (`idcategoria`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`produto` (
  `idproduto` INT NOT NULL AUTO_INCREMENT,
  `idvendedor` INT NOT NULL,
  `idcategoria` INT NOT NULL,
  `nome` VARCHAR(45) NULL,
  `numero_paginas` VARCHAR(45) NULL,
  `editora` VARCHAR(45) NULL,
  `classificacao_etaria` INT NULL,
  `data_publicacao` DATE NULL,
  `preco` FLOAT NULL,
  `quantidade` INT NULL,
  `descricao` TEXT NULL,
  `autor` VARCHAR(45) NULL,
  `isbn` CHAR(13) NULL,
  `dimensoes` VARCHAR(50) NULL,
  `idioma` VARCHAR(45) NULL,
  `estado_livro` ENUM('Usado', 'Novo') NULL,
  `paginas_faltando` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `folhas_amareladas` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `paginas_rasgadas` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `anotacoes` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `lombada_danificada` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `capa_danificada` ENUM('Sim', 'Não') NULL DEFAULT 'Não',
  `estado_detalhado` TEXT NULL,
  `status` ENUM('Disponivel', 'Vendido') NULL DEFAULT 'Disponivel',
  PRIMARY KEY (`idproduto`),
  INDEX `fk_produto_cadastro_vendedor1_idx` (`idvendedor` ASC) VISIBLE,
  INDEX `fk_produto_categoria1_idx` (`idcategoria` ASC) VISIBLE,
  CONSTRAINT `fk_produto_cadastro_vendedor1`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `banco`.`vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_produto_categoria1`
    FOREIGN KEY (`idcategoria`)
    REFERENCES `banco`.`categoria` (`idcategoria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`pedido` (
  `idpedido` INT NOT NULL AUTO_INCREMENT,
  `valor_total` FLOAT NULL,
  `data_pedido` DATE NULL,
  `idusuario` INT NOT NULL,
  `status` ENUM('pendente', 'concluido', 'cancelado') NULL,
  PRIMARY KEY (`idpedido`),
  INDEX `fk_pedido_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_pedido_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`item_pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`item_pedido` (
  `iditem_pedido` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NULL,
  `idproduto` INT NOT NULL,
  `idpedido` INT NOT NULL,
  PRIMARY KEY (`iditem_pedido`, `idpedido`),
  INDEX `fk_item_pedido_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_item_pedido_pedido1_idx` (`idpedido` ASC) VISIBLE,
  CONSTRAINT `fk_item_pedido_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_pedido_pedido1`
    FOREIGN KEY (`idpedido`)
    REFERENCES `banco`.`pedido` (`idpedido`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`comunidades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`comunidades` (
  `idcomunidades` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(45) NULL,
  `imagem` LONGBLOB NULL,
  `criada_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `quantidade_usuarios` INT NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idcomunidades`),
  INDEX `fk_comunidades_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_comunidades_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`membros_comunidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`membros_comunidade` (
  `idmembros_comunidade` INT NOT NULL AUTO_INCREMENT,
  `entrou_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `idcomunidades` INT NOT NULL,
  PRIMARY KEY (`idmembros_comunidade`, `idcomunidades`),
  INDEX `fk_membros_comunidade_comunidades1_idx` (`idcomunidades` ASC) VISIBLE,
  CONSTRAINT `fk_membros_comunidade_comunidades1`
    FOREIGN KEY (`idcomunidades`)
    REFERENCES `banco`.`comunidades` (`idcomunidades`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`mensagens_chat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`mensagens_chat` (
  `idmensagens_chat` INT NOT NULL AUTO_INCREMENT,
  `mensagem` TEXT NULL,
  `enviada_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `idcomunidades` INT NOT NULL,
  PRIMARY KEY (`idmensagens_chat`),
  INDEX `fk_mensagens_chat_comunidades1_idx` (`idcomunidades` ASC) VISIBLE,
  CONSTRAINT `fk_mensagens_chat_comunidades1`
    FOREIGN KEY (`idcomunidades`)
    REFERENCES `banco`.`comunidades` (`idcomunidades`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`imagens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`imagens` (
  `idimagens` INT NOT NULL AUTO_INCREMENT,
  `imagem` LONGBLOB NULL,
  `idproduto` INT NOT NULL,
  PRIMARY KEY (`idimagens`),
  INDEX `fk_imagens_produto1_idx` (`idproduto` ASC) VISIBLE,
  CONSTRAINT `fk_imagens_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`carrinho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`carrinho` (
  `idcarrinho` INT NOT NULL AUTO_INCREMENT,
  `preco_unitario` DOUBLE(10,2) NULL,
  `idproduto` INT NOT NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idcarrinho`),
  INDEX `fk_carrinho_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_carrinho_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_carrinho_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrinho_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`favoritos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`favoritos` (
  `idfavoritos` INT NOT NULL AUTO_INCREMENT,
  `idproduto` INT NOT NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idfavoritos`),
  INDEX `fk_favoritos_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_favoritos_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_favoritos_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_favoritos_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`avaliacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`avaliacoes` (
  `idavaliacoes` INT NOT NULL AUTO_INCREMENT,
  `nota` INT NULL,
  `comentario` TEXT NULL,
  `data_avaliacao` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `idusuario` INT NOT NULL,
  `idvendedor` INT NOT NULL,
  PRIMARY KEY (`idavaliacoes`),
  INDEX `fk_avaliacoes_usuario1_idx` (`idusuario` ASC) VISIBLE,
  INDEX `fk_avaliacoes_vendedor1_idx` (`idvendedor` ASC) VISIBLE,
  CONSTRAINT `fk_avaliacoes_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_avaliacoes_vendedor1`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `banco`.`vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco`.`notificacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco`.`notificacoes` (
  `idnotificacoes` INT NOT NULL AUTO_INCREMENT,
  `mensagem` TEXT NULL,
  `lida` TINYINT NULL,
  `data_envio` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `idusuario` INT NOT NULL,
  `idvendedor` INT NOT NULL,
  `tipo` ENUM('avaliação', 'compra') NULL,
  PRIMARY KEY (`idnotificacoes`),
  INDEX `fk_notificacoes_usuario1_idx` (`idusuario` ASC) VISIBLE,
  INDEX `fk_notificacoes_vendedor1_idx` (`idvendedor` ASC) VISIBLE,
  CONSTRAINT `fk_notificacoes_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_notificacoes_vendedor1`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `banco`.`vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
