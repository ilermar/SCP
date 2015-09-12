
--
-- Base de datos: `scpdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citologycal_template`
--

CREATE TABLE IF NOT EXISTS `citologycal_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(10) NOT NULL,
  `json_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `clinic`
--

CREATE TABLE IF NOT EXISTS `clinic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(300) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` int(11) DEFAULT NULL COMMENT 'Código de estado\n',
  `phone_number` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `notes` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX_NAME` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=103 ;

--
-- Estructura de tabla para la tabla `doctor`
--

CREATE TABLE IF NOT EXISTS `doctor` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` int(11) DEFAULT NULL COMMENT 'Código de estado',
  `phone_number_1` varchar(15) DEFAULT NULL COMMENT 'Teléfono de casa',
  `phone_number_2` varchar(15) DEFAULT NULL COMMENT 'Teléfono de oficina',
  `phone_number_3` varchar(15) DEFAULT NULL COMMENT 'Teléfono móvil',
  `fax` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `specialty` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX_NAME` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Estructura Stand-in para la vista `full_labtest`
--
CREATE TABLE IF NOT EXISTS `full_labtest` (
`id` bigint(20)
,`key_prefix` varchar(5)
,`key_number` int(11)
,`register_date` timestamp
,`type` int(11)
,`patient_name` varchar(100)
,`doctor_name` varchar(100)
,`status` tinyint(3) unsigned
,`age` int(11)
,`notes` varchar(2000)
,`main_json_data` varchar(50)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `labtest`
--

CREATE TABLE IF NOT EXISTS `labtest` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `patient_age` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 = Citología 2 = Histeroscopía 4 = Histopatología 8 = Colposcopía 16 = Androscopía 32 = Especiales',
  `main_doctor_id` bigint(20) NOT NULL COMMENT 'Doctor a cargo. El valor se toma de la tabla usuarios, filtrado por los que tienen el campo profile = 6',
  `notes` varchar(2000) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `key_prefix` varchar(5) NOT NULL COMMENT 'Letra(s) prefijo para la llave del  estudio (B, C, CA) ',
  `key_number` int(11) NOT NULL COMMENT 'Número del estudio. Valor asignado  por el sistema siempre el MAX() + 1',
  `status` tinyint(3) unsigned NOT NULL COMMENT '0 = Sin diagnóstico 1 = Firmado',
  `main_json_data` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_prefix` (`key_prefix`,`key_number`),
  KEY `patient_id` (`patient_id`,`doctor_id`,`type`,`register_date`,`status`),
  KEY `main_doctor_id` (`main_doctor_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `main_json_data` (`main_json_data`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabla principal para los estudios del scp' AUTO_INCREMENT=8 ;

--
-- Estructura de tabla para la tabla `labtest_data`
--

CREATE TABLE IF NOT EXISTS `labtest_data` (
  `id` varchar(50) NOT NULL,
  `json_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `medical_record`
--

CREATE TABLE IF NOT EXISTS `medical_record` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `json_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` int(11) DEFAULT NULL COMMENT 'Código de estado',
  `phone_number_1` varchar(15) DEFAULT NULL COMMENT 'Teléfono de casa',
  `phone_number_2` varchar(15) DEFAULT NULL COMMENT 'Teléfono de oficina',
  `phone_number_3` varchar(15) DEFAULT NULL COMMENT 'Teléfono móvil',
  `email` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX_NAME` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Estructura de tabla para la tabla `reminder`
--

CREATE TABLE IF NOT EXISTS `reminder` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `register_date` date DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `notes` varchar(200) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reminder_user_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smtp_server` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `use_ssl` tinyint(1) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Estructura de tabla para la tabla `supportcode`
--

CREATE TABLE IF NOT EXISTS `supportcode` (
  `sp_code` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `profile` int(11) DEFAULT NULL COMMENT 'Valor de enumeración:\n1 = Caja\n2 = Recepción\n3 = Ayudante general\n4 = Doctor asociado\n5 = Paciente\n6 = Doctor a cargo',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'el sha256 del email + "#" + password.',
  `phone_number` varchar(100) NOT NULL COMMENT 'Teléfono(s) de contacto con el usuario\n',
  `last_login` date DEFAULT NULL,
  `last_change_pwd` date DEFAULT NULL,
  `patient_id` bigint(20) DEFAULT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `INDEX_EMAIL` (`email`),
  KEY `fk_user_patient_idx` (`patient_id`),
  KEY `fk_user_doctor_idx` (`doctor_id`),
  KEY `INDEX_NAME` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Estructura para la vista `full_labtest`
--
DROP TABLE IF EXISTS `full_labtest`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `full_labtest` AS select `lt`.`id` AS `id`,`lt`.`key_prefix` AS `key_prefix`,`lt`.`key_number` AS `key_number`,`lt`.`register_date` AS `register_date`,`lt`.`type` AS `type`,`p`.`name` AS `patient_name`,`d`.`name` AS `doctor_name`,`lt`.`status` AS `status`,`lt`.`patient_age` AS `age`,`lt`.`notes` AS `notes`,`lt`.`main_json_data` AS `main_json_data` from ((`labtest` `lt` left join `patient` `p` on((`lt`.`patient_id` = `p`.`id`))) left join `doctor` `d` on((`lt`.`doctor_id` = `d`.`id`)));

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `labtest`
--
ALTER TABLE `labtest`
  ADD CONSTRAINT `labtest_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `labtest_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `labtest_ibfk_4` FOREIGN KEY (`main_doctor_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reminder`
--
ALTER TABLE `reminder`
  ADD CONSTRAINT `reminder_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
