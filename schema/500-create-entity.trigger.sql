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

DELIMITER $$

CREATE TRIGGER `%DATABASE%`.create_entity
AFTER INSERT ON `%DATABASE%`.`entity`
FOR EACH ROW
BEGIN
  INSERT INTO `%DATABASE%`.`group` (
    `name`,
    `entity_id`,
    `admin`,
    `created_by`,
    `locale_id`
  ) VALUES
  ('ADMIN', NEW.id, TRUE, NEW.created_by, NEW.locale_id),
  ('USERS', NEW.id, FALSE,NEW.created_by, NEW.locale_id);

  INSERT INTO `%DATABASE%`.`user_to_entity` (
    `user_id`,
    `entity_id`,
    `created_by`
  ) VALUES (NEW.created_by, NEW.id, NEW.created_by);
END$$

DELIMITER ;
