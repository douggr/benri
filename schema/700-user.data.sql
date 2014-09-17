/*
 * base/zf-rest
 *
 * @link https://svn.locness.com.br/svn/base/trunk/zf-rest for the canonical source repository
 * @version 1.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL log_output = 'TABLE' */;
/*!40101 SET GLOBAL general_log = 'ON' */;

-- ---------------------------------------------------------------------------
-- Data for table `%DATABASE%`.`user`
-- ---------------------------------------------------------------------------
LOCK TABLES `%DATABASE%`.`user` WRITE;
/*!40000 ALTER TABLE `%DATABASE%`.`user` DISABLE KEYS */;
INSERT INTO `%DATABASE%`.`user` (
  `email`,
  `username`,
  `password`,
  `status`,
  `admin`,
  `token`,
  `api_key`,
  `api_secret`,
  `visibility`
) VALUES
('installer'        , 'installer'        , '$2y$10$P/                                                   ', '0', '0', 'G', 'G                               ', 'G                                                           ', 'PRIVATE'),
('admin@example.com', 'admin@example.com', '$2y$10$P/KHjeHX8JVRPZ5aLeBxburpGSQ3FNrkQdoP5lLvJH4FZUNuv4EiK', '0', '1', 'A', '43yc3REynzkgn4c4nMcPgMWcxn364EEk', 'YY3K3ss3JqTCLsMk3LdVvWFDLsWUYTD4TYDKM4fJJyDyHDYTdEWdkJskgFKY', 'PRIVATE');

/*!40000 ALTER TABLE `%DATABASE%`.`user` ENABLE KEYS */;
UNLOCK TABLES;
