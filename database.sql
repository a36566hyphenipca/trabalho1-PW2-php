- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 18 mars 2026 à 20:54
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trabalho-php`
--

-- --------------------------------------------------------

--
-- Structure de la table `alunos`
--

CREATE TABLE `alunos` (
  `ID` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `mail` varchar(50) NOT NULL,
  `morada` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `alunos`
--

INSERT INTO `alunos` (`ID`, `nome`, `data nascimento`, `telefone`, `mail`, `morada`) VALUES
(2, 'ines maia', '2007-05-16', '916344796', 'ines@teste.pt', 'altos moinhos , eiriz'),
(3, 'leonor', NULL, '2345672345', 'leonor@teste.pt', 'jhg');

-- --------------------------------------------------------

--
-- Structure de la table `curso`
--

CREATE TABLE `curso` (
  `ID` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `numero maximo alunos` int(30) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `curso`
--

INSERT INTO `curso` (`ID`, `nome`, `numero maximo alunos`, `ativo`) VALUES
(1, 'engenheria infomatiqua', 30, 1),
(2, 'medecina', 30, 1),
(3, 'medecina', 30, 1);

-- --------------------------------------------------------

--
-- Structure de la table `ficha aluno`
--

CREATE TABLE `ficha aluno` (
  `ID` int(11) NOT NULL,
  `aluno ID` int(30) NOT NULL,
  `foto` text NOT NULL,
  `estado` varchar(50) NOT NULL,
  `observações` varchar(50) NOT NULL,
  `data submissão` datetime NOT NULL,
  `gestor ID` int(11) DEFAULT NULL,
  `data validacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ficha aluno`
--

INSERT INTO `ficha aluno` (`ID`, `aluno ID`, `foto`, `estado`, `observações`, `data submissão`, `gestor ID`, `data validacao`) VALUES
(1, 1, 'uploads/aluno_1_1773694279.jpg', 'aprovada', '', '0000-00-00 00:00:00', NULL, NULL),
(2, 2, 'uploads/aluno_2_1773860995.jpeg', 'rejeitada', '', '0000-00-00 00:00:00', NULL, '2026-03-18 19:15:31'),
(3, 3, '', 'aprovada', '', '0000-00-00 00:00:00', NULL, '2026-03-18 19:37:08');

-- --------------------------------------------------------

--
-- Structure de la table `foncionario`
--

CREATE TABLE `foncionario` (
  `ID` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `mail` varchar(30) NOT NULL,
  `telefone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gestor pedagogico`
--

CREATE TABLE `gestor pedagogico` (
  `ID` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `mail` varchar(30) NOT NULL,
  `telefone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notas`
--

CREATE TABLE `notas` (
  `ID` int(11) NOT NULL,
  `pauta ID` int(30) NOT NULL,
  `aluno ID` int(30) NOT NULL,
  `nota` decimal(4,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notas`
--

INSERT INTO `notas` (`ID`, `pauta ID`, `aluno ID`, `nota`) VALUES
(1, 2, 0, NULL),
(2, 1, 0, NULL),
(3, 1, 1, 20.0);

-- --------------------------------------------------------

--
-- Structure de la table `pautas`
--

CREATE TABLE `pautas` (
  `ID` int(11) NOT NULL,
  `uc ID` int(30) NOT NULL,
  `ano letivo` int(30) NOT NULL,
  `epoca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pautas`
--

INSERT INTO `pautas` (`ID`, `uc ID`, `ano letivo`, `epoca`) VALUES
(1, 2, 2025, 'normal'),
(2, 2, 2025, 'recurso');

-- --------------------------------------------------------

--
-- Structure de la table `pedido matricula`
--

CREATE TABLE `pedido matricula` (
  `ID` int(11) NOT NULL,
  `aluno ID` int(30) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `observações` varchar(260) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `foncionario ID` int(11) NOT NULL,
  `curso ID` int(11) NOT NULL,
  `data decisao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pedido matricula`
--

INSERT INTO `pedido matricula` (`ID`, `aluno ID`, `estado`, `observações`, `data`, `foncionario ID`, `curso ID`, `data decisao`) VALUES
(1, 2, 'aprovado', '', '0000-00-00 00:00:00', 0, 1, NULL),
(2, 3, 'aprovado', '', '2026-03-18 19:48:28', 0, 2, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `plano estudo`
--

CREATE TABLE `plano estudo` (
  `ID` int(11) NOT NULL,
  `curso ID` int(30) NOT NULL,
  `uc ID` int(30) NOT NULL,
  `ano` int(11) NOT NULL,
  `semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `plano estudo`
--

INSERT INTO `plano estudo` (`ID`, `curso ID`, `uc ID`, `ano`, `semestre`) VALUES
(1, 1, 0, 1, 1),
(2, 1, 2, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `uc`
--

CREATE TABLE `uc` (
  `ID` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `professor` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `uc`
--

INSERT INTO `uc` (`ID`, `nome`, `professor`) VALUES
(1, 'PROGRAMACAO WEB', 'INES MAIA'),
(2, 'matematica discreta', 'PROF maria');

-- --------------------------------------------------------

--
-- Structure de la table `unidade curricular`
--

CREATE TABLE `unidade curricular` (
  `ID` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `professor` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`ID`, `nome`, `mail`, `password`, `role`) VALUES
(11, 'Carlos Funcionario', 'funcionario@teste.pt', '$2y$10$8jW0fMo.L/b19mMkpJJ7teytsxi9/B296pbn5xdvq2oybIFB2.PHO', 'funcionario'),
(12, 'Gestor Pedagogico', 'gestor@teste.pt', '$2y$10$8jW0fMo.L/b19mMkpJJ7teytsxi9/B296pbn5xdvq2oybIFB2.PHO', 'gestor'),
(13, 'ines maia', 'ines@teste.pt', '$2y$10$dvwkWXQIMtTaSRPztkCMuO/HNUp7KDwT.4UEcv1.PTbH.H3X6/EyO', 'aluno'),
(14, 'leonor', 'leonor@teste.pt', '$2y$10$dSu344fy1BRgl6OQTHhXOetueszF5BMcH4vj/zqMlhKW6IaMkxWJy', 'aluno');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `ficha aluno`
--
ALTER TABLE `ficha aluno`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `foncionario`
--
ALTER TABLE `foncionario`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `gestor pedagogico`
--
ALTER TABLE `gestor pedagogico`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `pautas`
--
ALTER TABLE `pautas`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `pedido matricula`
--
ALTER TABLE `pedido matricula`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `plano estudo`
--
ALTER TABLE `plano estudo`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `uc`
--
ALTER TABLE `uc`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `unidade curricular`
--
ALTER TABLE `unidade curricular`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `alunos`
--
ALTER TABLE `alunos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `curso`
--
ALTER TABLE `curso`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `ficha aluno`
--
ALTER TABLE `ficha aluno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `foncionario`
--
ALTER TABLE `foncionario`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gestor pedagogico`
--
ALTER TABLE `gestor pedagogico`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notas`
--
ALTER TABLE `notas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `pautas`
--
ALTER TABLE `pautas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `pedido matricula`
--
ALTER TABLE `pedido matricula`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `plano estudo`
--
ALTER TABLE `plano estudo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `uc`
--
ALTER TABLE `uc`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `unidade curricular`
--
ALTER TABLE `unidade curricular`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
