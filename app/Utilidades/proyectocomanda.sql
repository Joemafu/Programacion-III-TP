-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-06-2023 a las 20:12:39
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`, `contadorClientes`) VALUES
(1, 'cerrada', 0),
(2, 'cerrada', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(5) NOT NULL,
  `nombreCliente` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tiempoEstimado` int(3) NOT NULL,
  `foto` varchar(75) DEFAULT NULL,
  `codigoSeguimiento` varchar(5) NOT NULL,
  `idMesa` int(5) NOT NULL,
  `puntuacionMesa` int(2) NOT NULL,
  `puntuacionRestaurante` int(2) NOT NULL,
  `puntuacionMozo` int(2) NOT NULL,
  `puntuacionCocinero` int(2) NOT NULL,
  `resenia` text NOT NULL,
  `valorTotal` double NOT NULL,
  `fecha` varchar(50) NOT NULL,
  `entregadoATiempo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `nombreCliente`, `estado`, `tiempoEstimado`, `foto`, `codigoSeguimiento`, `idMesa`, `puntuacionMesa`, `puntuacionRestaurante`, `puntuacionMozo`, `puntuacionCocinero`, `resenia`, `valorTotal`, `fecha`, `entregadoATiempo`) VALUES
(1, 'Juan', 'listo para servir', 25, 'fotoDePedidos/fotoDeJuan', '', 0, 0, 0, 0, 0, '', 0, '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productopedidos`
--

CREATE TABLE `productopedidos` (
  `id` int(5) NOT NULL,
  `idPedido` int(5) NOT NULL,
  `idProducto` int(5) NOT NULL,
  `idEmpleado` int(5) NOT NULL,
  `cantidad` int(5) NOT NULL,
  `valorSubtotal` double NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tiempoEstimado` int(5) DEFAULT NULL,
  `entregadoATiempo` varchar(50) DEFAULT NULL,
  `rolPreparador` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `tipo`, `contadorVendidos`) VALUES
(1, 'hamburguesa', '26.00', 'comida', 0),
(2, 'daiquiri', '26.00', 'trago', 0),
(3, 'ipa', '11.00', 'cerveza', 0),
(4, 'tiramisu', '11.00', 'postre', 0),
(5, 'bife', '30.99', 'postre', 0),
(6, 'milanesa con fritas', '34.99', 'comida', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `rol`, `suspendido`, `contadorOperaciones`) VALUES
(1, 'socioTest', 'socioTest', 'socio', 0, 0),
(2, 'mozoTest', 'mozoTest', 'mozo', 0, 0),
(3, 'bartenderTest', 'bartenderTest', 'bartender', 0, 0),
(4, 'cerveceroTest', 'cerveceroTest', 'cervecero', 0, 0),
(5, 'cocineroTest', 'cocineroTest', 'cocinero', 0, 0),
(6, 'altaTest', 'pa$$', 'mozo', 0, 0),
(7, 'altaTest5', 'pa$$word', 'mozo', 0, 0),
(8, 'altaTest6', 'altaTest6', 'bartender', 0, 0);

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
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productopedidos`
--
ALTER TABLE `productopedidos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
