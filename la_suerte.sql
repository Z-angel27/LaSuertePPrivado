-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 12:45 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `la_suerte`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `fecha_nacimiento`, `creado_en`) VALUES
(1, 'Angel Zacarias', '46626443', '2002-10-04', '2025-11-10 18:30:03'),
(2, 'Pedro tzunun', '456456456', '2006-01-03', '2025-11-10 18:54:33'),
(3, 'Pedro tzunun', '66663333', '2003-10-04', '2025-11-10 18:58:20'),
(4, 'Angel', '45455454', '2005-10-02', '2025-11-12 18:14:36'),
(5, 'Prueba', '999', '0000-00-00', '2025-11-12 18:16:46'),
(6, 'Rigoberto Tzunun', '12123232', '2000-11-11', '2025-11-12 19:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`) VALUES
(1, 'Atendedor 1');

-- --------------------------------------------------------

--
-- Table structure for table `sorteos`
--

CREATE TABLE `sorteos` (
  `id` int(11) NOT NULL,
  `fecha_sorteo` date NOT NULL,
  `tipo_sorteo_id` int(11) NOT NULL,
  `ocurrencia` int(11) NOT NULL,
  `numero_ganador` char(2) DEFAULT NULL,
  `fijado_en` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sorteos`
--

INSERT INTO `sorteos` (`id`, `fecha_sorteo`, `tipo_sorteo_id`, `ocurrencia`, `numero_ganador`, `fijado_en`, `creado_en`) VALUES
(1, '2025-11-11', 2, 1, '05', '2025-11-10 18:58:36', '2025-11-10 18:30:03'),
(2, '2025-11-10', 2, 1, '10', '2025-11-10 18:31:22', '2025-11-10 18:31:22'),
(3, '2025-11-11', 1, 3, NULL, NULL, '2025-11-10 18:54:33'),
(4, '2025-11-10', 1, 3, '01', '2025-11-10 18:54:59', '2025-11-10 18:54:59'),
(5, '2025-11-11', 3, 2, '12', '2025-11-10 19:05:04', '2025-11-10 19:00:24'),
(6, '2025-11-11', 3, 1, '12', '2025-11-10 19:05:09', '2025-11-10 19:04:49'),
(7, '2025-11-13', 1, 1, NULL, NULL, '2025-11-12 18:14:36'),
(8, '2025-11-12', 1, 1, NULL, NULL, '2025-11-12 18:16:46'),
(9, '2025-11-13', 2, 1, NULL, NULL, '2025-11-12 19:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_sorteo`
--

CREATE TABLE `tipos_sorteo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tasa_pago` decimal(10,2) NOT NULL,
  `ocurrencias_diarias` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipos_sorteo`
--

INSERT INTO `tipos_sorteo` (`id`, `nombre`, `tasa_pago`, `ocurrencias_diarias`) VALUES
(1, 'La Santa', '25.00', 3),
(2, 'La Rifa', '70.00', 1),
(3, 'El Sorteo', '150.00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL DEFAULT 'empleado',
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasena`, `rol`, `activo`, `creado_en`, `actualizado_en`) VALUES
(1, 'admin', '$2y$12$aJ13pckVMYiZkCn8wLzuuOY5JmvKQ5a.c2tujUV.stooC5Ezx4yKC', 'admin', 1, '2025-11-12 18:43:22', '2025-11-12 18:49:50'),
(2, 'empleado1', '$2y$12$tOobSUUF18ZPU7e81feuCuAuN0E7YhdrPUnLxIKw8TR7HXd3mYmjS', 'empleado', 1, '2025-11-12 18:43:22', '2025-11-12 18:49:50');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `sorteo_id` int(11) NOT NULL,
  `numero` char(2) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `empleado` varchar(120) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `fecha_limite_reclamo` date DEFAULT NULL,
  `reclamado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ventas`
--

INSERT INTO `ventas` (`id`, `cliente_id`, `sorteo_id`, `numero`, `monto`, `empleado`, `fecha_venta`, `fecha_limite_reclamo`, `reclamado`) VALUES
(1, 1, 1, '10', '1.00', 'Alfonso', '2025-11-10 18:30:03', '2025-11-18', 0),
(2, 2, 3, '10', '1.00', 'Alfonso', '2025-11-10 18:54:33', '2025-11-18', 0),
(3, 3, 1, '05', '1.00', 'Alfonso', '2025-11-10 18:58:20', '2025-11-18', 0),
(4, 1, 5, '11', '1.00', 'Alfonso', '2025-11-10 19:00:24', '2025-11-18', 0),
(5, 3, 6, '12', '2.00', 'Alfonso', '2025-11-10 19:04:49', '2025-11-18', 0),
(6, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:36', '2025-11-20', 0),
(7, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:36', '2025-11-20', 0),
(8, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:37', '2025-11-20', 0),
(9, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:37', '2025-11-20', 0),
(10, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:37', '2025-11-20', 0),
(11, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:37', '2025-11-20', 0),
(12, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:43', '2025-11-20', 0),
(13, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:43', '2025-11-20', 0),
(14, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:43', '2025-11-20', 0),
(15, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:44', '2025-11-20', 0),
(16, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:44', '2025-11-20', 0),
(17, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:44', '2025-11-20', 0),
(18, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:44', '2025-11-20', 0),
(19, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:44', '2025-11-20', 0),
(20, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:45', '2025-11-20', 0),
(21, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:48', '2025-11-20', 0),
(22, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:48', '2025-11-20', 0),
(23, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:49', '2025-11-20', 0),
(24, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:49', '2025-11-20', 0),
(25, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:57', '2025-11-20', 0),
(26, 4, 7, '21', '1.00', 'Alonso', '2025-11-12 18:14:57', '2025-11-20', 0),
(27, 5, 8, '00', '1.00', 'Test', '2025-11-12 18:16:46', '2025-11-19', 0),
(28, 6, 9, '10', '5.00', 'Alfonso', '2025-11-12 19:01:29', '2025-11-20', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sorteos`
--
ALTER TABLE `sorteos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_sorteo_id` (`tipo_sorteo_id`),
  ADD KEY `idx_sorteos_fecha` (`fecha_sorteo`);

--
-- Indexes for table `tipos_sorteo`
--
ALTER TABLE `tipos_sorteo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idx_usuario` (`usuario`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `sorteo_id` (`sorteo_id`),
  ADD KEY `idx_ventas_fecha` (`fecha_venta`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sorteos`
--
ALTER TABLE `sorteos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tipos_sorteo`
--
ALTER TABLE `tipos_sorteo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sorteos`
--
ALTER TABLE `sorteos`
  ADD CONSTRAINT `sorteos_ibfk_1` FOREIGN KEY (`tipo_sorteo_id`) REFERENCES `tipos_sorteo` (`id`);

--
-- Constraints for table `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`sorteo_id`) REFERENCES `sorteos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
