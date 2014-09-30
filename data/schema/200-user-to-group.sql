/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.3
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

-- ---------------------------------------------------------------------------
-- Table structure for table `%DATABASE%`.`user_to_group`
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `%DATABASE%`.`user_to_group`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`user_to_group` (
  `user_id`       INTEGER       NOT NULL,
  `group_id`      INTEGER       NOT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`    INTEGER       NOT NULL,
  PRIMARY KEY (`user_id`, `group_id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `%DATABASE%`.`user_to_group`
  ADD CONSTRAINT `user_to_group_fk_created_by`
  FOREIGN KEY (`created_by`) REFERENCES `user`(`id`);

ALTER TABLE `%DATABASE%`.`user_to_group`
  ADD CONSTRAINT `user_to_group_fk_user_id`
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `%DATABASE%`.`user_to_group`
  ADD CONSTRAINT `user_to_group_fk_group_id`
  FOREIGN KEY (`group_id`) REFERENCES `group`(`id`);
