/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

CREATE TABLE `%DATABASE%`.`user` (
  `access_token`  TEXT          NOT NULL,
  `admin`         BOOLEAN       NOT NULL DEFAULT FALSE,
  `api_key`       CHAR(32)      NOT NULL,
  `api_secret`    CHAR(60)      NOT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email`         VARCHAR(254)  NOT NULL, /* RFC 3696 */
  `id`            INTEGER       NOT NULL AUTO_INCREMENT,
  `password`      CHAR(60)      NOT NULL,
  `token`         CHAR(60)      NOT NULL,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username`      VARCHAR(254)  NOT NULL,

  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE UNIQUE INDEX `user_email`
  USING BTREE ON `%DATABASE%`.`user` (`email`);

CREATE UNIQUE INDEX `user_username`
  USING BTREE ON `%DATABASE%`.`user` (`username`);
