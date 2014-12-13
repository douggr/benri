<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 2.0.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * {@inheritdoc}
 */
class ZfRest_Model_Row_Locale extends ZfRest_Db_Row
{
    /**
     * {@inheritdoc}
     */
    public function normalizeInput($input)
    {
        // name   varchar(100)
        // code   char(15)
        // active tinyint(1)

        if (isset($input->name)) {
            $this->name = $input->name;
        }

        if (isset($input->code)) {
            $this->code = $input->code;
        }

        if (isset($input->active)) {
            $this->active = $input->active;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _insert()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function _save()
    {
        if ('' === trim($this->name)) {
            $this->pushError('name', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if ('' === trim($this->code)) {
            $this->pushError('code', 'missing_field', 'ERR.MISSING_FIELD');
        }

        if ('' === trim($this->active)) {
            $this->pushError('active', 'missing_field', 'ERR.MISSING_FIELD');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _update()
    {
    }

    /**
     * Setter for `name`
     * @return mixed
     */
    final protected function setName($value)
    {
        return trim($value);
    }

    /**
     * Setter for `code`
     * @return mixed
     */
    final protected function setCode($value)
    {
        return trim($value);
    }

    /**
     * Setter for `active`
     * @return mixed
     */
    final protected function setActive($value)
    {
        return intval($value);
    }
}
