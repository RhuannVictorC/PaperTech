-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/11/2026 às 02:17
-- Versão do servidor: 10.11.19-MariaDB
-- Versão do PHP: 8.5.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `papertech`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL CHECK (`preco` >= 0),
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0 CHECK (`quantidade_estoque` >= 0),
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `categoria`, `marca`, `preco`, `quantidade_estoque`, `data_cadastro`) VALUES
(2, 'Caderno Universitário', 'Caderno de 200 folhas', 'Papelaria', 'Tilibra', 24.90, 50, '2026-11-22 20:39:45'),
(3, 'Caneta Esferográfica', 'Caneta básica', 'Papelaria', 'BIC', 2.90, 50, '2026-11-22 22:00:34'),
(4, 'Lápis Grafite', 'Lápis básico', 'Papelaria', 'Faber Castell', 2.50, 50, '2026-11-22 22:01:18'),
(5, 'Borracha Branca Escolar', 'Borracha escolar', 'Papelaria', 'Mercur', 2.00, 25, '2026-11-22 22:02:12'),
(6, 'Marca-Texto', 'Marca-Texto', 'Papelaria', 'Stabilo Boss', 14.90, 25, '2026-11-22 22:02:59'),
(7, 'Tesoura Escolar', 'Tesoura Escolar sem ponta', 'Papelaria', 'Tramontina', 9.90, 25, '2026-11-22 22:03:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `data_cadastro`) VALUES
(1, 'Carlos Silva', 'carlossilva@papertech.com', '$2y$12$17ZamaPSmfazaNspwY1.sObT/mq1Idie3ZUvznG3hqq6UpOdxwB8y', 'admin', '2026-11-22 18:50:09'),
(2, 'Ana Carla', 'anacarla@papertech.com', '$2y$12$7X37/4Lb1ZFfS9CnfPZiNuZTL2VLx.NUzPkMPn207bbNMnafI9UDG', 'admin', '2026-11-22 20:01:36'),
(4, 'João Silva', 'joaosilva@papertech.com', '$2y$12$jRpSGTPmqo4sqTUBa9qGYeNPI232TeCT5T6UZ4mniuPzpFENoMKZG', 'funcionario', '2026-11-22 20:59:45');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
