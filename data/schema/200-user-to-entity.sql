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
-- Table structure for table `%DATABASE%`.`user_to_entity`
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `%DATABASE%`.`user_to_entity`;
CREATE TABLE IF NOT EXISTS `%DATABASE%`.`user_to_entity` (
  `user_id`       INTEGER       NOT NULL,
  `entity_id`     INTEGER       NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`    INTEGER       NOT NULL,
  PRIMARY KEY (`user_id`, `entity_id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE `%DATABASE%`.`user_to_entity`
  ADD CONSTRAINT `user_to_entity_fk_created_by`
  FOREIGN KEY (`created_by`) REFERENCES `user`(`id`);

ALTER TABLE `%DATABASE%`.`user_to_entity`
  ADD CONSTRAINT `user_to_entity_fk_user_id`
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `%DATABASE%`.`user_to_entity`
  ADD CONSTRAINT `user_to_entity_fk_entity_id`
  FOREIGN KEY (`entity_id`) REFERENCES `entity`(`id`);
