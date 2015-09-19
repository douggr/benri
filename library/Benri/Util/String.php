<?php

/**
 * Helpful string class.
 */
class Benri_Util_String
{
    /**
     * Returns given string as a dash_erized word.
     *
     * @param string $str String to dasherize
     * @param string $replacement Replacement to be used as "dash"
     * @return string The dash_erized form of the given `$str`
     */
    public static function dasherize($str, $replacement = '_')
    {
        return preg_replace_callback(
            '/([A-Z0-9-\s]+)/',
            function ($match) use ($replacement) {
                return $replacement . strtolower($match[1]);
            }, lcfirst($str)
        );
    }

    /**
     * @param string $string The string to slugfy
     * @param string $replacement Replacement to be used as the slug separator
     * @return string The slugy'd form of the given `$str`
     */
    public static function slugfy($string, $replacement = '-')
    {
        $string = static::removeponctuation(trim($string));
        $string = static::accentremove($string);

        return strtolower(preg_replace('/[^\w]+/', $replacement, $string));
    }

    /**
     * Returns given string as a camelCased word.
     *
     * @param string $str
     * @param bool $ucfirst wheter to uppercase the first character
     * @return string
     */
    public static function camelize($str, $ucfirst = false)
    {
        $replace = str_replace(
            ' ',
            '',
            ucwords(str_replace(['_', '-'], ' ', strtolower($str)))
        );

        if (!$ucfirst) {
            return lcfirst($replace);
        }

        return $replace;
    }

    /**
     * Quotes the string so that it can be used as Javascript string constants
     * for example.
     *
     * @param string $value The value to escape
     * @return string
     */
    public static function escape($value)
    {
        return strtr(
            $value,
            [
                "\r" => '\\r',
                "\n" => '\\n',
                "\'" => '\\\'',
                "\t" => '\\t',
                '/'  => '\\/',
                '"'  => '\\\\\'',
                '\\' => '\\\\\\',
            ]
        );
    }

    /**
     * Replace all ponctuation from the given string.
     *
     * @param string $str
     * @param string $replacement Replacement to apply
     * @return string
     */
    public static function removeponctuation($str, $replacement = '')
    {
        return preg_replace(
            '@(\xBB|\xAB|!|\xA1|%|,|:|;|\(|\)|\&|\'|"|\.|-|\/|\?|\\\)@',
            $replacement,
            $str
        );
    }

    /**
     * Translate characters to match \w regex.
     *
     * @param string $string to translate
     * @return string
     */
    public static function accentremove($str)
    {
        return str_replace(
            [
                'Á', 'á', 'à', 'À', 'â', 'Â', 'ä', 'Ä', 'ã', 'Ã', 'å', 'Å', 'ð',
                'é', 'É', 'È', 'è', 'Ê', 'ê', 'Ë', 'ë', 'í', 'Í', 'ì', 'Ì', 'î',
                'Î', 'ï', 'Ï', 'ñ', 'Ñ', 'ó', 'Ó', 'Ò', 'ò', 'Ô', 'ô', 'Ö', 'ö',
                'õ', 'Õ', 'Ú', 'ú', 'ù', 'Ù', 'û', 'Û', 'ü', 'Ü', 'ý', 'Ý', 'ÿ',
                'Ç', 'ç',
            ],

            [
                'A', 'a', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'o',
                'e', 'E', 'E', 'e', 'E', 'e', 'E', 'e', 'i', 'I', 'i', 'I', 'i',
                'I', 'i', 'I', 'n', 'N', 'o', 'O', 'O', 'o', 'O', 'o', 'O', 'o',
                'o', 'O', 'U', 'u', 'u', 'U', 'u', 'U', 'u', 'U', 'y', 'Y', 'y',
                'C', 'c',
            ],

            $str
        );
    }

    /**
     * Returns a random string with the given length and given string of
     * allowed characters.
     *
     * @param int $length The length of the random string
     * @param string $allowedChars Allowed chars
     * @return string
     */
    public static function random(
        $length         = 8,
        $allowedChars   = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXY346789'
    ) {
        $return     = '';
        $hashLength = strlen($allowedChars) - 1;

        for (;$length > 0; --$length) {
            $return .= $allowedChars{rand(0, $hashLength)};
        }

        return str_shuffle($return);
    }

    /**
     * Creates a password hash.
     *
     * @param string $raw The password in raw format
     * @return string Returns the hashed password, or `null` on failure
     */
    public static function password($raw)
    {
        return password_hash($raw, PASSWORD_BCRYPT);
    }

    /**
     * Verifies if the given (raw) password matches a hash.
     *
     * @param string $raw The password in raw format
     * @param string $hash A hash created by Benri_Util_String::password()
     * @return bool `true` if the password and hash match, or `false`
     *  otherwise
     */
    public static function verifyPassword($raw, $hash)
    {
        return password_verify($raw, $hash);
    }
}
