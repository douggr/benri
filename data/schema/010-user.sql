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

-- ---------------------------------------------------------------------------
-- Table structure for table `%DATABASE%`.`user`
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `%DATABASE%`.`user`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`user` (
  `id`            INTEGER       NOT NULL AUTO_INCREMENT,

  `email`         VARCHAR(254)  NOT NULL,  /* RFC 3696 */
  `username`      VARCHAR(254)  NOT NULL,
  `password`      CHAR(60)      NOT NULL,
  `admin`         BOOLEAN       NOT NULL DEFAULT FALSE,
  `token`         CHAR(60)      NOT NULL,
  `api_key`       CHAR(32)      NOT NULL,
  `api_secret`    CHAR(60)      NOT NULL,

  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE INDEX `idx_created_at`
  ON `%DATABASE%`.`user` (`created_at`);

CREATE UNIQUE INDEX `user_email`
  USING BTREE ON `%DATABASE%`.`user` (`email`);

CREATE UNIQUE INDEX `user_username`
  USING BTREE ON `%DATABASE%`.`user` (`username`);

CREATE UNIQUE INDEX `user_consumer_token`
  USING BTREE ON `%DATABASE%`.`user` (`api_key`, `api_secret`);
