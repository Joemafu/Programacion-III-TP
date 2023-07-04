-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2023 a las 22:44:04
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyectocomanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(5) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `contadorClientes` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`, `contadorClientes`) VALUES
(1, 'disponible', 3),
(2, 'con cliente comiendo', 1),
(3, 'disponible', 2),
(4, 'cerrado', 7),
(5, 'disponible', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(5) NOT NULL,
  `nombreCliente` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tiempoEstimado` int(3) NOT NULL DEFAULT 0,
  `foto` varchar(75) DEFAULT NULL,
  `codigoSeguimiento` varchar(5) NOT NULL,
  `idMesa` int(5) NOT NULL,
  `puntuacionMesa` int(2) DEFAULT NULL,
  `puntuacionRestaurante` int(2) DEFAULT NULL,
  `puntuacionMozo` int(2) DEFAULT NULL,
  `puntuacionCocinero` int(2) DEFAULT NULL,
  `resenia` text DEFAULT NULL,
  `valorTotal` double DEFAULT NULL,
  `fecha` varchar(50) NOT NULL DEFAULT current_timestamp(),
  `entregadoATiempo` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `nombreCliente`, `estado`, `tiempoEstimado`, `foto`, `codigoSeguimiento`, `idMesa`, `puntuacionMesa`, `puntuacionRestaurante`, `puntuacionMozo`, `puntuacionCocinero`, `resenia`, `valorTotal`, `fecha`, `entregadoATiempo`) VALUES
(45, 'Federico', 'servido', 50, '../FotosClientes/45.jpg', 'EA845', 2, 6, 9, 10, 9, 'La comida muy rica, el lugar agradable, la atención muy cordial.', 125.25, '2023-07-04', 'si');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productopedidos`
--

CREATE TABLE `productopedidos` (
  `id` int(5) NOT NULL,
  `idPedido` int(5) NOT NULL,
  `idProducto` int(5) NOT NULL,
  `idEmpleado` int(5) DEFAULT NULL,
  `cantidad` int(5) NOT NULL,
  `valorSubtotal` double NOT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `tiempoEstimado` int(5) DEFAULT NULL,
  `entregadoATiempo` varchar(50) DEFAULT NULL,
  `rolPreparador` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productopedidos`
--

INSERT INTO `productopedidos` (`id`, `idPedido`, `idProducto`, `idEmpleado`, `cantidad`, `valorSubtotal`, `estado`, `tiempoEstimado`, `entregadoATiempo`, `rolPreparador`) VALUES
(84, 45, 8, 5, 2, 69.98, 'servido', 30, 'si', 'cocinero'),
(85, 45, 9, 4, 1, 7.99, 'servido', 15, 'si', 'cervecero'),
(86, 45, 10, 3, 1, 12.29, 'servido', 25, 'si', 'bartender');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `contadorVendidos` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `tipo`, `contadorVendidos`) VALUES
(1, 'hamburguesa', '26.00', 'comida', 0),
(2, 'martini', '26.00', 'trago', 0),
(3, 'ipa', '11.00', 'cerveza', 0),
(4, 'tiramisu', '11.00', 'postre', 0),
(5, 'bife', '30.99', 'postre', 0),
(6, 'milanesa con fritas', '34.99', 'comida', 0),
(7, 'milanesa a caballo', '34.99', 'comida', 7),
(8, 'hamburguesa de garbanzo', '34.99', 'comida', 14),
(9, 'corona', '7.99', 'cerveza', 7),
(10, 'daikiri', '12.29', 'trago', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(5) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `suspendido` tinyint(1) NOT NULL,
  `contadorOperaciones` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `rol`, `suspendido`, `contadorOperaciones`) VALUES
(1, 'socioTest', 'socioTest', 'socio', 0, 3),
(2, 'mozoTest', 'mozoTest', 'mozo', 0, 3),
(3, 'bartenderTest', 'bartenderTest', 'bartender', 0, 2),
(4, 'cerveceroTest', 'cerveceroTest', 'cervecero', 0, 2),
(5, 'cocineroTest', 'cocineroTest', 'cocinero', 0, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productopedidos`
--
ALTER TABLE `productopedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `productopedidos`
--
ALTER TABLE `productopedidos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
