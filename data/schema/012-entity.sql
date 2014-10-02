/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.4
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

DROP TABLE IF EXISTS `%DATABASE%`.`entity`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`entity` (
  `id`            INTEGER       NOT NULL AUTO_INCREMENT,

  `name`          VARCHAR(200)  NOT NULL,
  `slug`          VARCHAR(200)  NOT NULL,
  `description`   TEXT,
  `location`      TEXT,
  `url`           VARCHAR(2048) NOT NULL DEFAULT '',  /* RFC 2616 */
  `email`         VARCHAR(254)  NOT NULL DEFAULT '',  /* RFC 3696 */
  `visibility`    ENUM('PUBLIC', 'PRIVATE') DEFAULT 'PUBLIC',

  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`    INTEGER       NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `%DATABASE%`.`entity`
  ADD CONSTRAINT `entity_fk_created_by`
  FOREIGN KEY (`created_by`) REFERENCES `user`(`id`);

CREATE UNIQUE INDEX `entity_name`
  USING BTREE ON `%DATABASE%`.`entity` (`name`);

CREATE UNIQUE INDEX `entity_slug`
  USING BTREE ON `%DATABASE%`.`entity` (`slug`);
