/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 2.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

DROP TABLE IF EXISTS `%DATABASE%`.`group`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`group` (
  `id`            INTEGER       NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)  NOT NULL,
  `description`   TEXT,
  `active`        BOOLEAN       NOT NULL DEFAULT TRUE,
  `admin`         BOOLEAN       NOT NULL DEFAULT FALSE,
  `entity_id`     INTEGER       NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`    INTEGER       NOT NULL,
  PRIMARY KEY (`id`, `entity_id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `%DATABASE%`.`group`
  ADD CONSTRAINT `group_fk_created_by`
  FOREIGN KEY (`created_by`) REFERENCES `user`(`id`);

ALTER TABLE `%DATABASE%`.`group`
  ADD CONSTRAINT `group_fk_entity`
  FOREIGN KEY (`entity_id`) REFERENCES `entity`(`id`);
