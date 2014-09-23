/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

-- ---------------------------------------------------------------------------
-- Table structure for table `%DATABASE%`.`locale`
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `%DATABASE%`.`locale`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`locale` (
  `id`            INTEGER       NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)  NOT NULL,
  `code`          VARCHAR(15)   NOT NULL,
  `active`        BOOLEAN       NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE UNIQUE INDEX `locale_code`
  ON `%DATABASE%`.`locale` (`code`);
