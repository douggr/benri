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

CREATE TRIGGER `%DATABASE%`.create_user
AFTER INSERT ON `%DATABASE%`.`user`
FOR EACH ROW
BEGIN
  INSERT INTO `%DATABASE%`.`user_to_entity` (
    `user_id`,
    `entity_id`,
    `created_by`
  ) VALUES (NEW.id, 1, NEW.id);
END$$

DELIMITER ;
