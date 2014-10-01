<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.4
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * Representation of date and time. 
 */
class ZfRest_Util_DateTime extends DateTime
{
    /**
     * {@inheritdoc}
     */
    private $_format = parent::ISO8601;

    /**
     * ctor. Returns new ZfRest_Util_DateTime object
     *
     * @param string A date/time string.
     * @param DateTimeZone A DateTimeZone object representing the timezone
     * @return ZfRest_Util_DateTime A new ZfRest_Util_DateTime instance
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
     * {@inheritdoc}
     */
    public function setFormat($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Returns date formatted according to given format.
     *
     * @param string format accepted by date()
     * @return string
     */
    public function format($format = null)
    {
        if (null === $format) {
            $format = $this->_format;
        }

        return parent::format($format);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->format();
    }
}
