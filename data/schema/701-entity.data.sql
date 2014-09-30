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
-- Data for table `%DATABASE%`.`entity`
-- ---------------------------------------------------------------------------
LOCK TABLES `%DATABASE%`.`entity` WRITE;
/*!40000 ALTER TABLE `%DATABASE%`.`entity` DISABLE KEYS */;
INSERT INTO `%DATABASE%`.`entity` (
  `name`,
  `slug`,
  `created_by`
) VALUES
('SITE', 'site', 1);

/*!40000 ALTER TABLE `%DATABASE%`.`entity` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `%DATABASE%`.`user_to_entity` WRITE;
/*!40000 ALTER TABLE `%DATABASE%`.`user_to_entity` DISABLE KEYS */;
INSERT INTO `%DATABASE%`.`user_to_entity` (
  `user_id`,
  `entity_id`,
  `created_by`
) VALUES (2, 1, 1);

/*!40000 ALTER TABLE `%DATABASE%`.`user_to_entity` ENABLE KEYS */;
UNLOCK TABLES;
