-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema banco2.0
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema banco2.0
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `banco2.0` DEFAULT CHARACTER SET utf8 ;
USE `banco2.0` ;

-- -----------------------------------------------------
-- Table `banco2.0`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`usuario` (
  `idusuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `telefone` INT NULL,
  `foto_de_perfil` LONGBLOB NULL,
  PRIMARY KEY (`idusuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`login_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`login_usuario` (
  `idlogin` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idlogin`),
  INDEX `fk_login_usuario_cadastro_usuario_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_login_usuario_cadastro_usuario`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`usuario_compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`usuario_compra` (
  `idusuario_compra` INT NOT NULL AUTO_INCREMENT,
  `pais` VARCHAR(45) NULL,
  `cidade` VARCHAR(45) NULL,
  `estado` CHAR(2) NULL,
  `nome_completo` VARCHAR(145) NULL,
  `telefone` VARCHAR(45) NULL,
  `cep` VARCHAR(8) NULL,
  `rua` VARCHAR(45) NULL,
  `bairro` VARCHAR(45) NULL,
  `cpf` VARCHAR(8) NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idusuario_compra`),
  INDEX `fk_usuario_compra_cadastro_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_usuario_compra_cadastro_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`vendedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`vendedor` (
  `idvendedor` INT NOT NULL AUTO_INCREMENT,
  `nome_completo` VARCHAR(45) NULL,
  `idade` INT NULL,
  `email` VARCHAR(45) NOT NULL,
  `senha` VARCHAR(45) NULL,
  `cpf` CHAR(11) NULL,
  `cnpj` CHAR(14) NULL,
  `foto_de_perfil` LONGBLOB NULL,
  `data_nascimento` DATE NULL,
  PRIMARY KEY (`idvendedor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`produto` (
  `idproduto` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `numero_paginas` VARCHAR(45) NULL,
  `editora` VARCHAR(45) NULL,
  `classificacao_etaria` INT NULL,
  `data_publicacao` DATE NULL,
  `preco` FLOAT NULL,
  `quantidade` INT NULL,
  `descricao` VARCHAR(1750) NULL,
  `idvendedor` INT NOT NULL,
  `categoria` VARCHAR(45) NULL,
  `autor` VARCHAR(45) NULL,
  `isbn` CHAR(13) NULL,
  `dimensoes` VARCHAR(50) NULL,
  `idioma` VARCHAR(45) NULL,
  `estado_livro` VARCHAR(45) NULL,
  `estado_detalhado` VARCHAR(1750) NULL,
  PRIMARY KEY (`idproduto`),
  INDEX `fk_produto_cadastro_vendedor1_idx` (`idvendedor` ASC) VISIBLE,
  CONSTRAINT `fk_produto_cadastro_vendedor1`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `banco2.0`.`vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`pedido` (
  `idpedido` INT NOT NULL AUTO_INCREMENT,
  `valor_total` FLOAT NULL,
  `data_pedido` DATE NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idpedido`),
  INDEX `fk_pedido_cadastro_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_pedido_cadastro_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`item_pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`item_pedido` (
  `iditem_pedido` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NULL,
  `idproduto` INT NOT NULL,
  `idpedido` INT NOT NULL,
  PRIMARY KEY (`iditem_pedido`, `idpedido`),
  INDEX `fk_item_pedido_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_item_pedido_pedido1_idx` (`idpedido` ASC) VISIBLE,
  CONSTRAINT `fk_item_pedido_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco2.0`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_pedido_pedido1`
    FOREIGN KEY (`idpedido`)
    REFERENCES `banco2.0`.`pedido` (`idpedido`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`comunidades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`comunidades` (
  `idcomunidades` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(45) NULL,
  `imagem` LONGBLOB NULL,
  `criada_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `quantidade_usuarios` INT NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idcomunidades`),
  INDEX `fk_comunidades_cadastro_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_comunidades_cadastro_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`membros_comunidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`membros_comunidade` (
  `idmembros_comunidade` INT NOT NULL AUTO_INCREMENT,
  `entrou_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `idcomunidades` INT NOT NULL,
  PRIMARY KEY (`idmembros_comunidade`, `idcomunidades`),
  INDEX `fk_membros_comunidade_comunidades1_idx` (`idcomunidades` ASC) VISIBLE,
  CONSTRAINT `fk_membros_comunidade_comunidades1`
    FOREIGN KEY (`idcomunidades`)
    REFERENCES `banco2.0`.`comunidades` (`idcomunidades`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`mensagens_chat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`mensagens_chat` (
  `idmensagens_chat` INT NOT NULL AUTO_INCREMENT,
  `mensagem` TEXT NULL,
  `enviada_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `idcomunidades` INT NOT NULL,
  PRIMARY KEY (`idmensagens_chat`),
  INDEX `fk_mensagens_chat_comunidades1_idx` (`idcomunidades` ASC) VISIBLE,
  CONSTRAINT `fk_mensagens_chat_comunidades1`
    FOREIGN KEY (`idcomunidades`)
    REFERENCES `banco2.0`.`comunidades` (`idcomunidades`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`imagens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`imagens` (
  `idimagens` INT NOT NULL AUTO_INCREMENT,
  `imagem` LONGBLOB NULL,
  `idproduto` INT NOT NULL,
  PRIMARY KEY (`idimagens`),
  INDEX `fk_imagens_produto1_idx` (`idproduto` ASC) VISIBLE,
  CONSTRAINT `fk_imagens_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco2.0`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`carrinho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`carrinho` (
  `idcarrinho` INT NOT NULL AUTO_INCREMENT,
  `preco_unitario` DOUBLE(10,2) NULL,
  `idproduto` INT NOT NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idcarrinho`),
  INDEX `fk_carrinho_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_carrinho_cadastro_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_carrinho_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco2.0`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrinho_cadastro_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `banco2.0`.`favoritos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco2.0`.`favoritos` (
  `idfavoritos` INT NOT NULL AUTO_INCREMENT,
  `idusuario` INT NOT NULL,
  `idproduto` INT NOT NULL,
  PRIMARY KEY (`idfavoritos`),
  INDEX `fk_favoritos_usuario1_idx` (`idusuario` ASC) VISIBLE,
  INDEX `fk_favoritos_produto1_idx` (`idproduto` ASC) VISIBLE,
  CONSTRAINT `fk_favoritos_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `banco2.0`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_favoritos_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `banco2.0`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
