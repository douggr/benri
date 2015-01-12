<?php
/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/**
 * Representation of date and time.
 *
 * @link http://php.net/manual/en/class.datetime.php DateTime
 */
class ZfRest_Util_DateTime extends DateTime
{
    /**
     * The format of the output date string.
     *
     * Valid formats are explained in
     * [Date and Time Formats](http://php.net/manual/en/datetime.formats.php).
     *
     * @var string
     */
    private $_format = parent::ISO8601;

    /**
    * Returns date formatted according to given format.
    *
    * If `$format` is `null`, it'll use `ZfRest_Util_DateTime::$_format`.
    *
    * @param string $format Format accepted by
    *  [date()](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters)
    * @return string
    * @see ZfRest_Util_DateTime::setFormat() ZfRest_Util_DateTime::setFormat()
    */
    public function format($format = null)
    {
        if (null === $format) {
            $format = $this->_format;
        }

        return parent::format($format);
    }

    /**
     * Getter for `ZfRest_Util_DateTime::$_format` property.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
    * Setter for `ZfRest_Util_DateTime::$_format`.
    *
    * @param string $format Format accepted by
    *  [date()](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters)
    * @return ZfRest_Util_DateTime
    */
    public function setFormat($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
    * Returns new ZfRest_Util_DateTime object.
    *
    * @param string $time A date/time string
    *   Format accepted by
    *   [date()](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters).
    * @param DateTimeZone $timezone A
    *   [DateTimeZone](http://php.net/manual/en/class.datetimezone.php)
    *   object representing the timezone
    * @return ZfRest_Util_DateTime A new ZfRest_Util_DateTime instance
    * @see ZfRest_Util_DateTime::format() ZfRest_Util_DateTime::format()
    */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if (preg_match('/[\d+]{10}/', $time)) {
            return static::createFromFormat('U', $time, $timezone);
        } else {
            return parent::__construct($time, $timezone);
        }
    }

    /**
     * @return string
     * @see ZfRest_Util_DateTime::format() ZfRest_Util_DateTime::format()
     */
    public function __toString()
    {
        return $this->format();
    }
}
