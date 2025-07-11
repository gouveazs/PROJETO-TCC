-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema vendedor
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema vendedor
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `vendedor` DEFAULT CHARACTER SET utf8 ;
USE `vendedor` ;

-- -----------------------------------------------------
-- Table `vendedor`.`cadastro_vendedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendedor`.`cadastro_vendedor` (
  `idvendedor` INT NOT NULL AUTO_INCREMENT,
  `nome_completo` VARCHAR(45) NULL,
  `data_nascimento` DATE NULL,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `cpf` CHAR(11) NULL,
  `cnpj` CHAR(14) NULL,
  `foto_de_perfil` LONGBLOB,
  PRIMARY KEY (`idvendedor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `vendedor`.`login_vendedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendedor`.`login_vendedor` (
  `idlogin_vendedor` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `idvendedor` INT NOT NULL,
  PRIMARY KEY (`idlogin_vendedor`, `idvendedor`),
  INDEX `fk_login_vendedor_cadastro_vendedor_idx` (`idvendedor` ASC) VISIBLE,
  CONSTRAINT `fk_login_vendedor_cadastro_vendedor`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `vendedor`.`cadastro_vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `vendedor`.`produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendedor`.`produto` (
  `idproduto` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `numero_paginas` INT NULL,
  `editora` VARCHAR(45) NULL,
  `autor` VARCHAR(45) NULL,
  `classificacao_idade` INT NULL,
  `data_publicacao` DATE NULL,
  `preco` FLOAT NULL,
  `quantidade` INT NULL,
  `categoria` VARCHAR(45) NULL,
  `imagem` LONGBLOB NULL,
  `descricao` VARCHAR(445) NULL,
  `idvendedor` INT NOT NULL,
  PRIMARY KEY (`idproduto`, `idvendedor`),
  INDEX `fk_produto_cadastro_vendedor1_idx` (`idvendedor` ASC) VISIBLE,
  CONSTRAINT `fk_produto_cadastro_vendedor1`
    FOREIGN KEY (`idvendedor`)
    REFERENCES `vendedor`.`cadastro_vendedor` (`idvendedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `vendedor`.`pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendedor`.`pedido` (
  `idpedido` INT NOT NULL,
  `valor_total` FLOAT NULL,
  `data_pedido` DATE NULL,
  `idusuario` INT NULL,
  PRIMARY KEY (`idpedido`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `vendedor`.`item_pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendedor`.`item_pedido` (
  `iditem_pedido` INT NOT NULL,
  `quantidade` INT NULL,
  `idproduto` INT NOT NULL,
  `idpedido` INT NOT NULL,
  PRIMARY KEY (`iditem_pedido`, `idproduto`, `idpedido`),
  INDEX `fk_item_pedido_produto1_idx` (`idproduto` ASC) VISIBLE,
  INDEX `fk_item_pedido_pedido1_idx` (`idpedido` ASC) VISIBLE,
  CONSTRAINT `fk_item_pedido_produto1`
    FOREIGN KEY (`idproduto`)
    REFERENCES `vendedor`.`produto` (`idproduto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_pedido_pedido1`
    FOREIGN KEY (`idpedido`)
    REFERENCES `vendedor`.`pedido` (`idpedido`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
