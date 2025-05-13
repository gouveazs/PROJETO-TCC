-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema usuario
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema usuario
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `usuario` DEFAULT CHARACTER SET utf8 ;
USE `usuario` ;

-- -----------------------------------------------------
-- Table `usuario`.`cadastro_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario`.`cadastro_usuario` (
  `idusuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `telefone` INT NULL,
  PRIMARY KEY (`idusuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usuario`.`login_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario`.`login_usuario` (
  `idlogin` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `idusuario` INT NOT NULL,
  PRIMARY KEY (`idlogin`, `idusuario`),
  INDEX `fk_login_usuario_cadastro_usuario_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_login_usuario_cadastro_usuario`
    FOREIGN KEY (`idusuario`)
    REFERENCES `usuario`.`cadastro_usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usuario`.`usuario_compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario`.`usuario_compra` (
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
  PRIMARY KEY (`idusuario_compra`, `idusuario`),
  INDEX `fk_usuario_compra_cadastro_usuario1_idx` (`idusuario` ASC) VISIBLE,
  CONSTRAINT `fk_usuario_compra_cadastro_usuario1`
    FOREIGN KEY (`idusuario`)
    REFERENCES `usuario`.`cadastro_usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

