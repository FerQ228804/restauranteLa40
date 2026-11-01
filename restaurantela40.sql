-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-11-2026 a las 09:16:36
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
-- Base de datos: `restaurantela40`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `clave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('registro_abierto', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(10) UNSIGNED NOT NULL,
  `clave` varchar(100) NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `clave`, `descripcion`) VALUES
(1, '*', 'Todos los permisos'),
(2, 'usuarios.gestionar', 'Gestionar usuarios y roles'),
(3, 'configuracion.gestionar', 'Configurar registro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria`, `nombre`, `descripcion`, `precio`, `imagen`) VALUES
(1, 'Hamburguesas', 'Hamburguesa Especial', 'Carne de res 150g, queso cheddar, tocino y vegetales frescos.', 15.00, 'hamburguesa-especial.jpg'),
(2, 'Pizzas', 'Pizza Artesanal', 'Masa madre, salsa de tomate natural, mozzarella y pepperoni.', 18.00, 'pizza-artesanal.jpg'),
(3, 'Bebidas', 'Limonada de Coco', 'Refrescante bebida tropical con crema de coco y limón.', 5.00, 'limonada-coco.jpg'),
(4, 'Perros Calientes', 'Perro Caliente Deluxe', 'Salchicha americana, queso fundido, tocineta, papa ripio y salsas de la casa.', 12.50, 'perro-deluxe.jpg'),
(5, 'Alitas y Acompañamientos', 'Alitas BBQ', '8 jugosas alitas bañadas en salsa BBQ artesanal, acompañadas de apio y zanahoria.', 14.00, 'alitas-bbq.jpg'),
(6, 'Hamburguesas', 'Hamburguesa Ranchera', 'Carne de res 150g, queso doble crema, huevo frito, tocino crujiente y salsa BBQ.', 16.50, 'hamburguesa-ranchera.jpg'),
(7, 'Hamburguesas', 'Hamburguesa BBQ Tocino', 'Doble carne de res, aros de cebolla, doble tocino ahumado y salsa BBQ dulce.', 17.00, 'hamburguesa-bbq-tocino.jpg'),
(8, 'Hamburguesas', 'Hamburguesa Hawaiana', 'Carne de res 150g, rodaja de piña asada, jamón, queso mozzarella y vegetales frescos.', 15.50, 'hamburguesa-hawaiana.jpg'),
(9, 'Perros Calientes', 'Perro Caliente Ranchero', 'Salchicha suiza, queso fundido, huevo de codorniz, tocineta picada y salsa rosada.', 13.00, 'perro-ranchero.jpg'),
(10, 'Perros Calientes', 'Perro Caliente Mexicano', 'Salchicha picante, guacamole, pico de gallo, jalapeños y nachos triturados.', 14.00, 'perro-mexicano.jpg'),
(11, 'Perros Calientes', 'Perro Caliente Especial de la Casa', 'Salchicha americana gigante, queso doble crema, jamón, tocineta y lluvia de papas.', 15.00, 'perro-especial-casa.jpg'),
(12, 'Pizzas', 'Pizza Napolitana', 'Masa madre, tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva extra.', 19.00, 'pizza-napolitana.jpg'),
(13, 'Pizzas', 'Pizza Pepperoni & Queso', 'Extra queso mozzarella, doble porción de pepperoni madurado y salsa de la casa.', 17.50, 'pizza-pepperoni-queso.jpg'),
(14, 'Pizzas', 'Pizza Cuatro Quesos', 'Combinación perfecta de mozzarella, provolone, parmesano y queso azul.', 20.00, 'pizza-cuatro-quesos.jpg'),
(15, 'Lasañas', 'Lasaña Clásica Bolognesa', 'Capas de pasta con salsa boloñesa tradicional, bechamel y queso gratinado.', 14.00, 'lasana-clasica-bolognesa.jpg'),
(16, 'Lasañas', 'Lasaña Mixta (Carne y Pollo)', 'Pasta artesanal con pollo, carne, champiñones, salsa bechamel y queso fundido.', 16.00, 'lasana-mixta.jpg'),
(17, 'Lasañas', 'Lasaña de Pollo con Champiñones', 'Pechuga de pollo desmenuzada, champiñones frescos en salsa blanca y parmesano.', 15.00, 'lasana-pollo-champinones.jpg'),
(18, 'Lasañas', 'Lasaña Vegetariana', 'Láminas de pasta, espinaca, berenjena, calabacín, salsa de tomate y queso ricotta.', 14.50, 'lasana-vegetariana.jpg'),
(19, 'Alitas y Acompañamientos', 'Aros de Cebolla', 'Crujientes aros de cebolla apanados, servidos con salsa tártara de la casa.', 6.00, 'aros-cebolla.jpg'),
(20, 'Alitas y Acompañamientos', 'Alitas Picantes (Búfalo)', '8 alitas bañadas en salsa picante estilo Búfalo, servidas con aderezo ranch.', 14.50, 'alitas-picantes-bufalo.jpg'),
(21, 'Alitas y Acompañamientos', 'Papas a la Francesa Gratinadas', 'Porción de papas a la francesa cubiertas con queso fundido y trocitos de tocino.', 8.00, 'papas-francesa-gratinadas.jpg'),
(22, 'Bebidas', 'Limonada de Hierbabuena', 'Limonada natural frapeada con hojas de hierbabuena fresca.', 4.50, 'limonada-hierbabuena.jpg'),
(23, 'Bebidas', 'Limonada Cerezada', 'Bebida refrescante a base de limón con un toque dulce de cereza.', 4.50, 'limonada-cerezada.jpg'),
(24, 'Bebidas', 'Gaseosa Personal', 'Bebida carbonatada en presentación personal (Coca-Cola, Sprite, Quatro).', 2.50, 'gaseosa-personal.jpg'),
(25, 'Hamburguesas', 'Hamburguesa Doble Carne con Queso', 'Dos jugosas carnes de res, doble capa de queso cheddar, tocineta crujiente, cebolla morada y salsa especial en pan brioche.', 18.50, 'hamburguesa-doble-carne.jpg'),
(26, 'Hamburguesas', 'Hamburguesa Vegetariana de la Casa', 'Medallón de lentejas y champiñones, lechuga, tomate fresco, aguacate en rodajas, queso mozzarella y mayonesa de albahaca.', 15.00, 'hamburguesa-vegetariana.jpg'),
(27, 'Perros Calientes', 'Perro Caliente Hawaiano', 'Salchicha ahumada, piña caramelizada, jamón picado, queso fundido, papitas ripio y salsa de la casa.', 13.50, 'perro-hawaiano.jpg'),
(28, 'Perros Calientes', 'Perro Caliente Tricolor', 'Salchicha jumbo, carne desmechada, pollo desmechado, tocineta, triple capa de queso fundido y papas fosforito.', 16.00, 'perro-tricolor.jpg'),
(29, 'Pizzas', 'Pizza Hawaiana Clásica', 'Salsa de tomate, queso mozzarella, jugosos trozos de piña dulce y jamón ahumado.', 18.00, 'pizza-hawaiana.jpg'),
(30, 'Pizzas', 'Pizza Carnívora Suprema', 'Salsa de tomate, queso mozzarella, pepperoni, carne molida, jamón ahumado y trocitos de tocineta crujiente.', 21.00, 'pizza-carnivora-suprema.jpg'),
(31, 'Lasañas', 'Lasaña Tres Carnes', 'Capas de pasta con carne boloñesa, pollo desmechado y tocineta ahumada, bañadas en bechamel y gratinadas con queso.', 17.50, 'lasana-tres-carnes.jpg'),
(32, 'Lasañas', 'Lasaña Ranchera', 'Pasta fresca con trozos de salchicha ranchera, maíz tierno, salsa BBQ, bechamel y un toque de queso cheddar gratinado.', 15.50, 'lasana-ranchera.jpg'),
(33, 'Alitas y Acompañamientos', 'Dedos de Queso Empanizados', 'Bastones de queso mozzarella rebosados y fritos, acompañados de salsa de mora agridulce.', 7.50, 'dedos-queso.jpg'),
(34, 'Alitas y Acompañamientos', 'Papas Gajo con Romero', 'Gajos de papa rústicos sazonados con sal marina, aceite de oliva y finas hierbas, servidos con salsa de la casa.', 6.50, 'papas-gajo-romero.jpg'),
(35, 'Bebidas', 'Jugo Natural en Agua', 'Fruta fresca de temporada licuada en agua: mango, mora, maracuyá o lulo.', 3.50, 'jugo-natural-agua.jpg'),
(36, 'Bebidas', 'Té Hatsu Frío', 'Bebida refrescante de té blanco o negro con notas florales y frutales (botella de 400 ml).', 4.50, 'te-hatsu-frio.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'administrador'),
(2, 'usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(10) UNSIGNED NOT NULL,
  `permiso_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expira_en` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int(10) UNSIGNED NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `expira_en` (`expira_en`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD CONSTRAINT `tokens_recuperacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
