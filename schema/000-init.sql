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
-- Database structure for `%DATABASE%`
-- ---------------------------------------------------------------------------
DROP DATABASE IF EXISTS `%DATABASE%`;

CREATE DATABASE `%DATABASE%`
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

CREATE FUNCTION `%DATABASE%`.JSON_ESCAPE(str TEXT) RETURNS TEXT
  RETURN REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(str, ''), '\\', '\\u005C'), '''', '\\u0027'), '"',  '\\u0022'), '\n', '\\u000A')
