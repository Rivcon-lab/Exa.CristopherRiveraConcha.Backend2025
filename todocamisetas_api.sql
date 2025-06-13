-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2025 a las 07:52:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `todocamisetas_api`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `camisetas`
--

CREATE TABLE `camisetas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `club` varchar(100) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `tipo` enum('Local','Visita','3era Camiseta','Femenino Local','Niño') NOT NULL,
  `color` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` > 0),
  `precio_oferta` decimal(10,2) DEFAULT NULL CHECK (`precio_oferta` > 0),
  `detalles` text DEFAULT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `camisetas`
--

INSERT INTO `camisetas` (`id`, `titulo`, `club`, `pais`, `tipo`, `color`, `precio`, `precio_oferta`, `detalles`, `codigo_producto`, `created_at`, `updated_at`) VALUES
(2, 'Camiseta Visita 2025 – Real Madrid', 'Real Madrid', 'España', 'Visita', 'Negro', 55000.00, NULL, 'Temporada 2024-2025', 'RM2025V', '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(3, 'Camiseta Local 2025 – Barcelona', 'FC Barcelona', 'España', 'Local', 'Azul y Granate', 52000.00, 47000.00, 'Clásico diseño culé', 'FCB2025L', '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(4, 'Camiseta 3era 2025 – PSG', 'Paris Saint-Germain', 'Francia', '3era Camiseta', 'Rosa', 48000.00, 43000.00, 'Edición especial', 'PSG2025T', '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(5, 'Camiseta Femenino Local – Arsenal', 'Arsenal', 'Inglaterra', 'Femenino Local', 'Rojo y Blanco', 42000.00, NULL, 'Temporada femenina', 'ARS2025F', '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(7, 'Camiseta Local 2025 – Selección Chilena', 'Selección Chilena', 'Chile', 'Local', 'Rojo y Azul', 45000.00, 38000.00, 'Edición aniversario 2025', 'SCL2025L', '2025-06-13 03:13:15', '2025-06-13 03:13:15'),
(14, 'Test Camiseta', 'Test Club', 'Testlandia', 'Local', 'Verde', 10000.00, 9000.00, 'Prueba', 'TESTC123', '2025-06-13 04:42:39', '2025-06-13 04:42:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `camiseta_tallas`
--

CREATE TABLE `camiseta_tallas` (
  `id` int(11) NOT NULL,
  `camiseta_id` int(11) NOT NULL,
  `talla_id` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0 CHECK (`stock` >= 0),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `camiseta_tallas`
--

INSERT INTO `camiseta_tallas` (`id`, `camiseta_id`, `talla_id`, `stock`, `created_at`, `updated_at`) VALUES
(7, 2, 4, 18, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(8, 2, 5, 14, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(9, 2, 6, 6, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(10, 3, 2, 70, '2025-06-12 03:31:39', '2025-06-13 05:14:24'),
(11, 3, 3, 16, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(12, 3, 4, 22, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(13, 3, 5, 10, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(14, 3, 6, 5, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(15, 4, 1, 5, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(16, 4, 2, 8, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(17, 4, 3, 12, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(18, 4, 4, 15, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(19, 4, 5, 10, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(20, 5, 2, 6, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(21, 5, 3, 10, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(22, 5, 4, 8, '2025-06-12 03:31:39', '2025-06-12 03:31:39'),
(23, 5, 5, 4, '2025-06-12 03:31:39', '2025-06-12 03:31:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_comercial` varchar(100) NOT NULL,
  `rut` varchar(20) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `categoria` enum('Regular','Preferencial') DEFAULT 'Regular',
  `contacto_nombre` varchar(100) NOT NULL,
  `contacto_email` varchar(100) NOT NULL,
  `porcentaje_oferta` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_comercial`, `rut`, `direccion`, `categoria`, `contacto_nombre`, `contacto_email`, `porcentaje_oferta`, `created_at`, `updated_at`) VALUES
(1, '90minutos', '12345678-9', 'Providencia, Santiago', 'Preferencial', 'Juan Pérez', 'juan@90minutos.cl', 15.00, '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(2, 'tdeportes', '98765432-1', 'Las Condes, Santiago', 'Regular', 'María González', 'maria@tdeportes.cl', 0.00, '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(3, 'Deportes Chile', '11222333-4', 'Ñuñoa, Santiago', 'Regular', 'Carlos Ruiz', 'carlos@deporteschile.cl', 5.00, '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(4, 'Cliente Modificado', '99999999-9', 'prueba de cambio 456', 'Preferencial', 'Tester Mod', 'tester@fake.com', 10.00, '2025-06-13 04:21:31', '2025-06-13 05:30:40'),
(12, 'Cliente Nuevo Modificado', '99929999-9', 'prueba de cambio 456', 'Preferencial', 'Tester Mod', 'tester@fake.com', 10.00, '2025-06-13 05:32:27', '2025-06-13 05:32:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tallas`
--

CREATE TABLE `tallas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tallas`
--

INSERT INTO `tallas` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'XS', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(2, 'S', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(3, 'M', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(4, 'L', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(5, 'XL', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(6, 'XXL', '2025-06-12 03:31:38', '2025-06-12 03:31:38'),
(14, 'TestTalla', '2025-06-13 04:42:39', '2025-06-13 04:42:39');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `camisetas`
--
ALTER TABLE `camisetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `idx_club` (`club`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_codigo` (`codigo_producto`),
  ADD KEY `idx_precio` (`precio`);

--
-- Indices de la tabla `camiseta_tallas`
--
ALTER TABLE `camiseta_tallas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_camiseta_talla` (`camiseta_id`,`talla_id`),
  ADD KEY `idx_camiseta` (`camiseta_id`),
  ADD KEY `idx_talla` (`talla_id`),
  ADD KEY `idx_stock` (`stock`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rut` (`rut`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_rut` (`rut`);

--
-- Indices de la tabla `tallas`
--
ALTER TABLE `tallas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `camisetas`
--
ALTER TABLE `camisetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `camiseta_tallas`
--
ALTER TABLE `camiseta_tallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tallas`
--
ALTER TABLE `tallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `camiseta_tallas`
--
ALTER TABLE `camiseta_tallas`
  ADD CONSTRAINT `camiseta_tallas_ibfk_1` FOREIGN KEY (`camiseta_id`) REFERENCES `camisetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `camiseta_tallas_ibfk_2` FOREIGN KEY (`talla_id`) REFERENCES `tallas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
