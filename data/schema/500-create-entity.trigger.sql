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

DELIMITER $$

CREATE TRIGGER `%DATABASE%`.create_entity
AFTER INSERT ON `%DATABASE%`.`entity`
FOR EACH ROW
BEGIN
  INSERT INTO `%DATABASE%`.`group` (
    `name`,
    `entity_id`,
    `admin`,
    `created_by`
  ) VALUES
  ('ADMIN', NEW.id, TRUE, NEW.created_by),
  ('USERS', NEW.id, FALSE,NEW.created_by);
END$$

DELIMITER ;
