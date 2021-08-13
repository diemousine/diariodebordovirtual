-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 15-Out-2015 às 15:47
-- Versão do servidor: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pibid_dbv`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `instituicao`
--

CREATE TABLE IF NOT EXISTS `instituicao` (
  `idinstituicao` int(11) NOT NULL,
  `instdescricao` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `instituicao_coordenador`
--

CREATE TABLE IF NOT EXISTS `instituicao_coordenador` (
  `idinstituicao_coordenador` int(11) NOT NULL,
  `icinstituicao` int(11) NOT NULL,
  `iccoordenador` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `inst_loc_coord`
--

CREATE TABLE IF NOT EXISTS `inst_loc_coord` (
  `idinst_loc_coord` int(11) NOT NULL,
  `ilclocal` int(11) NOT NULL,
  `ilcinstituicao` int(11) NOT NULL,
  `ilccoordenador` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `local`
--

CREATE TABLE IF NOT EXISTS `local` (
  `idlocal` int(11) NOT NULL,
  `locdescricao` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `idlogin` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `senha` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `relato`
--

CREATE TABLE IF NOT EXISTS `relato` (
  `idrelato` int(11) NOT NULL,
  `relinstituicao` int(11) NOT NULL,
  `relcoordenador` int(11) NOT NULL,
  `rellocal` int(11) NOT NULL,
  `relsupervisor` int(11) NOT NULL,
  `relusuario` int(11) NOT NULL,
  `reldata` datetime NOT NULL,
  `reltitulo` varchar(64) DEFAULT NULL,
  `relato` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idusuario` int(11) NOT NULL,
  `usidlogin` int(11) NOT NULL,
  `nome` varchar(64) DEFAULT NULL,
  `vinculo` int(11) DEFAULT NULL,
  `instituicao` int(11) DEFAULT NULL,
  `coordenador` int(11) DEFAULT NULL,
  `local` int(11) DEFAULT NULL,
  `supervisor` int(11) DEFAULT NULL,
  `curso` varchar(64) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `foto` varchar(36) DEFAULT NULL,
  `autorizado` tinyint(4) DEFAULT '0',
  `ult_acesso` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vinculo`
--

CREATE TABLE IF NOT EXISTS `vinculo` (
  `idvinculo` int(11) NOT NULL,
  `vindescricao` varchar(16) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `vinculo`
--

INSERT INTO `vinculo` (`idvinculo`, `vindescricao`) VALUES
(3, 'Bolsista'),
(1, 'Coordenador'),
(2, 'Supervisor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `instituicao`
--
ALTER TABLE `instituicao`
  ADD PRIMARY KEY (`idinstituicao`), ADD UNIQUE KEY `instdescricao_UNIQUE` (`instdescricao`);

--
-- Indexes for table `instituicao_coordenador`
--
ALTER TABLE `instituicao_coordenador`
  ADD PRIMARY KEY (`idinstituicao_coordenador`), ADD KEY `idinstituicao_idx` (`icinstituicao`), ADD KEY `idcoordenador_idx` (`iccoordenador`);

--
-- Indexes for table `inst_loc_coord`
--
ALTER TABLE `inst_loc_coord`
  ADD PRIMARY KEY (`idinst_loc_coord`), ADD KEY `idinsti_local_idx` (`ilcinstituicao`), ADD KEY `idlocal_insti_idx` (`ilclocal`), ADD KEY `idilccoordenador_idx` (`ilccoordenador`);

--
-- Indexes for table `local`
--
ALTER TABLE `local`
  ADD PRIMARY KEY (`idlocal`), ADD UNIQUE KEY `locdescricao_UNIQUE` (`locdescricao`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`idlogin`) COMMENT 'Identificador de Login do Usuário';

--
-- Indexes for table `relato`
--
ALTER TABLE `relato`
  ADD PRIMARY KEY (`idrelato`), ADD KEY `relato_instituicao_idx` (`relinstituicao`), ADD KEY `relato_coordenador_idx` (`relcoordenador`), ADD KEY `relato_local_idx` (`rellocal`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`), ADD KEY `usuario_login_idx` (`usidlogin`), ADD KEY `usuario_vinculo_idx` (`vinculo`), ADD KEY `usuario_instituicao_idx` (`instituicao`);

--
-- Indexes for table `vinculo`
--
ALTER TABLE `vinculo`
  ADD PRIMARY KEY (`idvinculo`), ADD UNIQUE KEY `descricao_UNIQUE` (`vindescricao`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `instituicao`
--
ALTER TABLE `instituicao`
  MODIFY `idinstituicao` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `instituicao_coordenador`
--
ALTER TABLE `instituicao_coordenador`
  MODIFY `idinstituicao_coordenador` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inst_loc_coord`
--
ALTER TABLE `inst_loc_coord`
  MODIFY `idinst_loc_coord` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `local`
--
ALTER TABLE `local`
  MODIFY `idlocal` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `idlogin` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `relato`
--
ALTER TABLE `relato`
  MODIFY `idrelato` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vinculo`
--
ALTER TABLE `vinculo`
  MODIFY `idvinculo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
