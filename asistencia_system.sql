-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-12-2025 a las 03:29:21
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
-- Base de datos: `asistencia_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `nombre`, `created_at`) VALUES
(4, 'admin', '$2y$10$CY/v/xrhHQiOkmmG9ipqk.HMXIib6eLv1qUmLJdBX6RjXWJJHAsRy', 'Administrador', '2025-11-23 04:22:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `tipo` enum('entrada','salida','refrigerio1_inicio','refrigerio1_fin','refrigerio2_inicio','refrigerio2_fin','refrigerio3_inicio','refrigerio3_fin') NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `timestamp_reg` timestamp NOT NULL DEFAULT current_timestamp(),
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `ip_origen` varchar(100) DEFAULT NULL,
  `estado` enum('puntual','tardanza','falta','invalid') DEFAULT 'puntual',
  `nota` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `empleado_id`, `dni`, `tipo`, `fecha`, `hora`, `timestamp_reg`, `lat`, `lng`, `ip_origen`, `estado`, `nota`) VALUES
(1, 1, '74859612', 'entrada', '2025-11-23', '00:03:20', '2025-11-23 05:03:20', -15.8695424, -70.0153856, '::1', 'invalid', NULL),
(2, 1, '74859612', 'entrada', '2025-11-27', '11:31:34', '2025-11-27 16:31:34', NULL, NULL, '::1', 'falta', NULL),
(3, 1, '74859612', 'salida', '2025-11-27', '11:40:02', '2025-11-27 16:40:02', NULL, NULL, '::1', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `dni`, `nombres`, `apellidos`, `edad`, `cargo`, `creado_en`) VALUES
(1, '74859612', 'Carlos Pedro', 'Miko Macro', 21, 'jefe', '2025-11-23 04:24:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `ref_inicio_min` time NOT NULL,
  `ref_inicio_max` time NOT NULL,
  `ref_fin_min` time NOT NULL,
  `ref_fin_max` time NOT NULL,
  `entrada` time NOT NULL,
  `salida` time NOT NULL,
  `ref1_inicio` time NOT NULL,
  `ref1_fin` time NOT NULL,
  `ref2_inicio` time NOT NULL,
  `ref2_fin` time NOT NULL,
  `ref3_inicio` time NOT NULL,
  `ref3_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `ref_inicio_min`, `ref_inicio_max`, `ref_fin_min`, `ref_fin_max`, `entrada`, `salida`, `ref1_inicio`, `ref1_fin`, `ref2_inicio`, `ref2_fin`, `ref3_inicio`, `ref3_fin`) VALUES
(1, '12:00:00', '12:15:00', '12:45:00', '13:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `idx_asistencias_fecha` (`fecha`),
  ADD KEY `idx_asistencias_dni` (`dni`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
