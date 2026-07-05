-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 05, 2026 at 05:54 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `loanlinkdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id_emprestimo` int NOT NULL,
  `id_item` int NOT NULL,
  `id_proprietario` int NOT NULL,
  `id_emprestado` int NOT NULL,
  `data_solicitacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_aprovacao` datetime DEFAULT NULL,
  `data_prevista_devolucao` date DEFAULT NULL,
  `data_devolucao` date DEFAULT NULL,
  `status` enum('pendente','emprestado','devolvido','cancelado') NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emprestimos`
--

INSERT INTO `emprestimos` (`id_emprestimo`, `id_item`, `id_proprietario`, `id_emprestado`, `data_solicitacao`, `data_aprovacao`, `data_prevista_devolucao`, `data_devolucao`, `status`) VALUES
(1, 27, 3, 4, '2026-06-29 15:54:17', '2026-07-02 10:37:01', '2026-07-09', '2026-07-02', 'devolvido'),
(2, 28, 3, 4, '2026-06-29 16:22:09', '2026-07-02 10:51:15', '2026-07-09', NULL, 'emprestado'),
(3, 41, 3, 4, '2026-06-29 16:40:20', '2026-07-02 10:50:12', '2026-07-09', NULL, 'emprestado'),
(4, 33, 3, 4, '2026-07-02 11:07:32', '2026-07-02 11:08:55', '2026-07-09', '2026-07-02', 'devolvido'),
(5, 37, 3, 4, '2026-07-02 11:31:35', NULL, NULL, NULL, 'cancelado'),
(6, 27, 3, 4, '2026-07-02 14:57:56', '2026-07-02 14:58:03', '2026-07-09', '2026-07-02', 'devolvido'),
(7, 37, 3, 4, '2026-07-05 11:34:13', NULL, NULL, NULL, 'cancelado'),
(8, 37, 3, 4, '2026-07-05 11:50:24', '2026-07-05 11:50:48', '2026-07-12', NULL, 'emprestado');

-- --------------------------------------------------------

--
-- Table structure for table `itens`
--

CREATE TABLE `itens` (
  `id_item` int NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `categoria` enum('Livros','Eletrônicos','Ferramentas','Materiais Escolares','Eletrodomésticos','Roupas e Acessórios','Esportes e Lazer','Instrumentos Musicais','Móveis','Veículos e Transporte','Outros') NOT NULL,
  `descricao` varchar(500) NOT NULL,
  `status` enum('disponivel','pendente','emprestado') NOT NULL DEFAULT 'disponivel',
  `id_proprietario` int NOT NULL,
  `foto_item` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `itens`
--

INSERT INTO `itens` (`id_item`, `nome`, `categoria`, `descricao`, `status`, `id_proprietario`, `foto_item`) VALUES
(27, 'Parafusadeira Makita', 'Ferramentas', 'Parafusadeira sem fio com bateria recarregável.', 'disponivel', 3, NULL),
(28, 'Jogo de Chaves', 'Ferramentas', 'Kit com chaves de fenda e Philips.', 'emprestado', 3, NULL),
(29, 'Notebook Dell', 'Eletrônicos', 'Notebook com 8GB de RAM e SSD de 256GB.', 'disponivel', 3, NULL),
(30, 'Projetor Epson', 'Eletrônicos', 'Projetor Full HD para apresentações.', 'disponivel', 3, NULL),
(32, 'Caixa de Som JBL', 'Eletrônicos', 'Caixa de som Bluetooth portátil.', 'disponivel', 3, NULL),
(33, 'Livro Dom Casmurro', 'Livros', 'Exemplar conservado de Machado de Assis.', 'emprestado', 3, NULL),
(34, 'Livro O Hobbit', 'Livros', 'Edição ilustrada em ótimo estado.', 'disponivel', 3, NULL),
(35, 'Livro História do Brasil', 'Livros', 'Livro didático sobre a história brasileira.', 'disponivel', 3, NULL),
(36, 'Bola de Futebol', 'Esportes e Lazer', 'Bola oficial para campo.', 'disponivel', 3, NULL),
(37, 'Raquete de Tênis', 'Esportes e Lazer', 'Raquete de alumínio para iniciantes.', 'emprestado', 3, NULL),
(38, 'Barraca de Camping', 'Esportes e Lazer', 'Barraca para quatro pessoas.', 'disponivel', 3, NULL),
(39, 'Violão Acústico', 'Instrumentos Musicais', 'Violão de aço em bom estado.', 'disponivel', 3, NULL),
(40, 'Teclado Musical Yamaha', 'Instrumentos Musicais', 'Teclado eletrônico com 61 teclas.', 'disponivel', 3, NULL),
(41, 'Memorias de um Adolescente Brasileiro na Alemanha Nazista', 'Livros', 'Livro histórico sobre brasileiros na alemanha nazista', 'emprestado', 3, 'uploads/foto_itens/6a36f0bb94529.PNG');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id_like` int NOT NULL,
  `id_postagem` int NOT NULL,
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postagens`
--

CREATE TABLE `postagens` (
  `id_postagem` int NOT NULL,
  `id_item` int NOT NULL,
  `data_postagem` datetime DEFAULT CURRENT_TIMESTAMP,
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `postagens`
--

INSERT INTO `postagens` (`id_postagem`, `id_item`, `data_postagem`, `ativo`) VALUES
(2, 41, '2026-06-28 16:55:06', 0),
(4, 27, '2026-06-28 18:06:03', 0),
(6, 28, '2026-06-29 16:15:28', 0),
(7, 33, '2026-07-02 11:07:04', 0),
(8, 37, '2026-07-02 11:31:26', 0),
(9, 27, '2026-07-02 14:57:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `telefone`, `email`, `senha`, `foto_perfil`) VALUES
(1, 'Renato', '11999999999', 'taubate@gmail.com', '$2y$10$IxuMs7XntwR1MJtbvdcMvuq8MKia47IOEHWsvdnbvnMss4oj.GjPq', NULL),
(2, 'Tiago', '11945094831', 'tiago123@gmail.com', '$2y$10$mbrc1kNfmyAsiYOkVnLyQOa5YRoM2yW6hQ6rhQkuxBzDMZvuOJb0a', NULL),
(3, 'admin', '11999999995', 'admin@gmail.com', '$2y$10$6xqIF3ArHiIr1wMIetCZFuv6TZ3I5SbVEOG0ukNxzRF5NejvrgJRi', 'uploads/foto_usuarios/f7937f6ccd6435cd93a4538be18fef0c.jpg'),
(4, 'Adalberto', '11945094898', 'teste123@gmail.com', '$2y$10$t3p3TYSIDHUAvb.SJ7ZdKeZGV5mBit4wrFDvMNUxy96qlBQjvs9Fm', 'uploads/foto_usuarios/6a3becae06f56.PNG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id_emprestimo`),
  ADD KEY `id_item` (`id_item`),
  ADD KEY `id_proprietario` (`id_proprietario`),
  ADD KEY `id_emprestado` (`id_emprestado`);

--
-- Indexes for table `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `fk_proprietario` (`id_proprietario`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `uk_postagem_usuario` (`id_postagem`,`id_usuario`),
  ADD KEY `fk_like_usuario` (`id_usuario`);

--
-- Indexes for table `postagens`
--
ALTER TABLE `postagens`
  ADD PRIMARY KEY (`id_postagem`),
  ADD KEY `fk_postagens_itens` (`id_item`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `telefone` (`telefone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id_emprestimo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `itens`
--
ALTER TABLE `itens`
  MODIFY `id_item` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id_like` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postagens`
--
ALTER TABLE `postagens`
  MODIFY `id_postagem` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `emprestimos_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `itens` (`id_item`),
  ADD CONSTRAINT `emprestimos_ibfk_2` FOREIGN KEY (`id_proprietario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `emprestimos_ibfk_3` FOREIGN KEY (`id_emprestado`) REFERENCES `usuarios` (`id_usuario`);

--
-- Constraints for table `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `fk_proprietario` FOREIGN KEY (`id_proprietario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_like_postagem` FOREIGN KEY (`id_postagem`) REFERENCES `postagens` (`id_postagem`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_like_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Constraints for table `postagens`
--
ALTER TABLE `postagens`
  ADD CONSTRAINT `fk_postagens_itens` FOREIGN KEY (`id_item`) REFERENCES `itens` (`id_item`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
