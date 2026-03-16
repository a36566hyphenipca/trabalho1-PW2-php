-- ============================================================
-- BASE DE DADOS: trabalho-php
-- Script completo — apaga e recria tudo
-- ============================================================

CREATE DATABASE IF NOT EXISTS `trabalho-php`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `trabalho-php`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `notas`;
DROP TABLE IF EXISTS `pautas`;
DROP TABLE IF EXISTS `pedido matricula`;
DROP TABLE IF EXISTS `ficha aluno`;
DROP TABLE IF EXISTS `plano estudo`;
DROP TABLE IF EXISTS `uc`;
DROP TABLE IF EXISTS `curso`;
DROP TABLE IF EXISTS `alunos`;
DROP TABLE IF EXISTS `foncionario`;
DROP TABLE IF EXISTS `gestor pedagogico`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
    `ID`       INT AUTO_INCREMENT PRIMARY KEY,
    `nome`     VARCHAR(150) NOT NULL,
    `mail`     VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role`     VARCHAR(30)  NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `curso` (
    `ID`                   INT AUTO_INCREMENT PRIMARY KEY,
    `nome`                 VARCHAR(200) NOT NULL,
    `numero maximo alunos` INT NOT NULL DEFAULT 30,
    `ativo`                TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE `uc` (
    `ID`        INT AUTO_INCREMENT PRIMARY KEY,
    `nome`      VARCHAR(200) NOT NULL,
    `professor` VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `plano estudo` (
    `ID`       INT AUTO_INCREMENT PRIMARY KEY,
    `curso ID` INT NOT NULL,
    `UC ID`    INT NOT NULL,
    `ano`      TINYINT NOT NULL,
    `semestre` TINYINT NOT NULL,
    UNIQUE KEY `unico` (`curso ID`, `UC ID`, `semestre`),
    FOREIGN KEY (`curso ID`) REFERENCES `curso`(`ID`) ON DELETE CASCADE,
    FOREIGN KEY (`UC ID`)    REFERENCES `uc`(`ID`)    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `alunos` (
    `ID`              INT AUTO_INCREMENT PRIMARY KEY,
    `nome`            VARCHAR(150) NOT NULL,
    `mail`            VARCHAR(150) NOT NULL UNIQUE,
    `morada`          VARCHAR(255) NULL,
    `data nascimento` DATE NULL,
    `telefone`        VARCHAR(20) NULL
) ENGINE=InnoDB;

CREATE TABLE `foncionario` (
    `ID`       INT AUTO_INCREMENT PRIMARY KEY,
    `nome`     VARCHAR(150) NOT NULL,
    `mail`     VARCHAR(150) NOT NULL UNIQUE,
    `telefone` VARCHAR(20) NULL
) ENGINE=InnoDB;

CREATE TABLE `gestor pedagogico` (
    `ID`       INT AUTO_INCREMENT PRIMARY KEY,
    `nome`     VARCHAR(150) NOT NULL,
    `mail`     VARCHAR(150) NOT NULL UNIQUE,
    `telefone` VARCHAR(20) NULL
) ENGINE=InnoDB;

CREATE TABLE `ficha aluno` (
    `ID`             INT AUTO_INCREMENT PRIMARY KEY,
    `aluno ID`       INT NOT NULL,
    `gestor ID`      INT NULL,
    `observacoes`    TEXT NULL,
    `data submissão` DATETIME NULL,
    `foto`           VARCHAR(255) NULL,
    `estado`         VARCHAR(20) NOT NULL DEFAULT 'rascunho',
    `data validacao` DATETIME NULL,
    FOREIGN KEY (`aluno ID`) REFERENCES `alunos`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `pedido matricula` (
    `ID`             INT AUTO_INCREMENT PRIMARY KEY,
    `aluno ID`       INT NOT NULL,
    `foncionario ID` INT NULL,
    `curso ID`       INT NOT NULL,
    `estado`         VARCHAR(20) NOT NULL DEFAULT 'pendente',
    `data`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data decisao`   DATETIME NULL,
    `observações`    TEXT NULL,
    FOREIGN KEY (`aluno ID`)  REFERENCES `alunos`(`ID`) ON DELETE CASCADE,
    FOREIGN KEY (`curso ID`)  REFERENCES `curso`(`ID`)  ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `pautas` (
    `ID`         INT AUTO_INCREMENT PRIMARY KEY,
    `UC ID`      INT NOT NULL,
    `ano letivo` VARCHAR(10) NOT NULL,
    `epoca`      VARCHAR(20) NOT NULL,
    UNIQUE KEY `unico_pauta` (`UC ID`, `ano letivo`, `epoca`),
    FOREIGN KEY (`UC ID`) REFERENCES `uc`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `notas` (
    `ID`       INT AUTO_INCREMENT PRIMARY KEY,
    `pauta ID` INT NOT NULL,
    `aluno ID` INT NOT NULL,
    `nota`     DECIMAL(4,1) NOT NULL DEFAULT 0.0,
    UNIQUE KEY `unico_nota` (`pauta ID`, `aluno ID`),
    FOREIGN KEY (`pauta ID`) REFERENCES `pautas`(`ID`) ON DELETE CASCADE,
    FOREIGN KEY (`aluno ID`) REFERENCES `alunos`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- UTILIZADORES DE TESTE (password: teste123)
-- ============================================================
INSERT INTO `users` (`nome`, `mail`, `password`, `role`) VALUES
('Ana Aluno',          'aluno@teste.pt',       '$2y$10$8jW0fMo.L/b19mMkpJJ7teytsxi9/B296pbn5xdvq2oybIFB2.PHO', 'aluno'),
('Carlos Funcionario', 'funcionario@teste.pt', '$2y$10$8jW0fMo.L/b19mMkpJJ7teytsxi9/B296pbn5xdvq2oybIFB2.PHO', 'funcionario'),
('Gestor Pedagogico',  'gestor@teste.pt',      '$2y$10$8jW0fMo.L/b19mMkpJJ7teytsxi9/B296pbn5xdvq2oybIFB2.PHO', 'gestor');

INSERT INTO `alunos` (`nome`, `mail`) VALUES ('Ana Aluno', 'aluno@teste.pt');
INSERT INTO `foncionario` (`nome`, `mail`) VALUES ('Carlos Funcionario', 'funcionario@teste.pt');
INSERT INTO `gestor pedagogico` (`nome`, `mail`) VALUES ('Gestor Pedagogico', 'gestor@teste.pt');
