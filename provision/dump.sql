DROP TABLE IF EXISTS categories;
CREATE TABLE `categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title_ua` VARCHAR(45) NOT NULL,
  `title_en` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

DROP TABLE IF EXISTS photo;
CREATE TABLE `photo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(200) NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `is_visible` INT NOT NULL DEFAULT 1,
  `slider_home` INT NOT NULL DEFAULT 0,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`));

DROP TABLE IF EXISTS photo_category;
CREATE TABLE `photo_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_photo` INT NOT NULL,
  `id_category` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_photo_idx` (`id_photo` ASC),
  INDEX `FK_category_idx` (`id_category` ASC),
  CONSTRAINT `FK_photo`
    FOREIGN KEY (`id_photo`)
    REFERENCES `photo` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `FK_category`
    FOREIGN KEY (`id_category`)
    REFERENCES `categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);

DROP TABLE IF EXISTS roles;
CREATE TABLE `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`));

DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(200) NOT NULL,
  `username` VARCHAR(200) NOT NULL,
  `password` VARCHAR(400) NOT NULL,
  `role_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_agent` TEXT NOT NULL,
  `ip` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_role_1_idx` (`role_id` ASC),
  CONSTRAINT `FK_role_1`
    FOREIGN KEY (`role_id`)
    REFERENCES `roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);

DROP TABLE IF EXISTS resources;
CREATE TABLE `resources` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `resource` VARCHAR(100) NOT NULL,
  `permission` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`));

DROP TABLE IF EXISTS permissions;
CREATE TABLE `permissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role_id` INT NOT NULL,
  `resource_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_role_idx` (`role_id` ASC),
  INDEX `FK_resource_idx` (`resource_id` ASC),
  CONSTRAINT `FK_role`
    FOREIGN KEY (`role_id`)
    REFERENCES `roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `FK_resource`
    FOREIGN KEY (`resource_id`)
    REFERENCES `resources` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);

ALTER TABLE `photo`
ADD COLUMN `title_ua` TEXT NOT NULL AFTER `created`,
ADD COLUMN `title_en` TEXT NOT NULL AFTER `title_ua`,
ADD COLUMN `description_ua` TEXT NOT NULL AFTER `title_en`,
ADD COLUMN `description_en` TEXT NOT NULL AFTER `description_ua`;

INSERT INTO `roles` (`id`, `name`) VALUES (1,'public');
INSERT INTO `roles` (`id`, `name`) VALUES (2,'admin');
INSERT INTO `roles` (`id`, `name`) VALUES (3,'user');

ALTER TABLE `users`
ADD UNIQUE INDEX `email_UNIQUE` (`email` ASC);

INSERT INTO `resources` (`resource`, `permission`) VALUES ('index', 'index');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('categories', 'add');

INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '1');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '2');

INSERT INTO `resources` (`resource`, `permission`) VALUES ('categories', 'list');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('categories', 'update');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('categories', 'delete');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('contact', 'index');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'list');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'photo');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'add');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'delete');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'getPhoto');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'update');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('user', 'login');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('user', 'logout');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('user', 'get');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('user', 'create');

INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '3');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '4');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '5');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '6');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '7');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '8');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '9');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '10');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '11');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '12');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '13');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '14');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '15');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '16');

INSERT INTO `categories` (`id`, `title_ua`, `title_en`) VALUES ('0', 'Всі', 'All');
INSERT INTO `resources` (`resource`, `permission`) VALUES ('categories', 'fullList');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('2', '17');

ALTER TABLE `photo`
CHANGE COLUMN `description_ua` `description_ua` TEXT NULL ,
CHANGE COLUMN `description_en` `description_en` TEXT NULL ;

ALTER TABLE `photo`
ADD COLUMN `vertical` TINYINT NULL AFTER `description_en`;

CREATE TABLE `photo_like` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo_id` INT NOT NULL,
  `IP` VARCHAR(45) NOT NULL,
  `user_agent` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `resources` (`resource`, `permission`) VALUES ('photo', 'setLike');
INSERT INTO `permissions` (`role_id`, `resource_id`) VALUES ('1', '18');
