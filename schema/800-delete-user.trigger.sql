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

CREATE TRIGGER `%DATABASE%`.delete_user
BEFORE DELETE ON `%DATABASE%`.`user`
FOR EACH ROW
BEGIN
  DELETE FROM `%DATABASE%`.`user_to_entity` WHERE `user_id` = OLD.id
END$$

DELIMITER ;
