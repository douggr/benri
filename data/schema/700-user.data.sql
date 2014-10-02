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

LOCK TABLES `%DATABASE%`.`user` WRITE;
/*!40000 ALTER TABLE `%DATABASE%`.`user` DISABLE KEYS */;
INSERT INTO `%DATABASE%`.`user` (
  `email`,
  `username`,
  `password`,
  `admin`,
  `token`,
  `api_key`,
  `api_secret`
) VALUES
('installer'        , 'installer'        , '$2y$10$P/                                                   ', '0', 'G', 'G                               ', 'G                                                           '),
('admin@example.com', 'admin@example.com', '$2y$10$P/KHjeHX8JVRPZ5aLeBxburpGSQ3FNrkQdoP5lLvJH4FZUNuv4EiK', '1', 'A', '43yc3REynzkgn4c4nMcPgMWcxn364EEk', 'YY3K3ss3JqTCLsMk3LdVvWFDLsWUYTD4TYDKM4fJJyDyHDYTdEWdkJskgFKY');

/*!40000 ALTER TABLE `%DATABASE%`.`user` ENABLE KEYS */;
UNLOCK TABLES;
