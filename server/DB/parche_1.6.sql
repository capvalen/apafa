ALTER TABLE `reunion` ADD `tiporeunion` INT NOT NULL AFTER `seccion`;
ALTER TABLE `reunion` CHANGE `tiporeunion` `tiporeunion` INT(11) NOT NULL DEFAULT '1';
CREATE TABLE `apafa`.`tiporeunion` (`id` INT NOT NULL , `descripcion` VARCHAR(250) NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `tiporeunion` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
INSERT INTO `tiporeunion` (`id`, `descripcion`) VALUES (null, 'Reunión general'), (null, 'Reunión de comités'), (null, 'Reuniones ordinarias');.
ALTER TABLE `reunion` CHANGE `tiporeunion` `tiporeunion` INT(11) NULL DEFAULT '1';