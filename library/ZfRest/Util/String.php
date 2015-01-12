<?php
/**
 * douggr/zf-rest
 *
 * @license http://opensource.org/license/MIT
 * @link    https://github.com/douggr/zf-rest
 * @version 2.1.0
 */

/**
 * Helpful string class.
 */
class ZfRest_Util_String
{
    /**
     * @var array
     */
    private static $plural = array(
        '/(quiz)$/i'                     => '$1zes',
        '/^(ox)$/i'                      => '$1en',
        '/([m|l])ouse$/i'                => '$1ice',
        '/(matr|vert|ind)ix|ex$/i'       => '$1ices',
        '/(x|ch|ss|sh)$/i'               => '$1es',
        '/([^aeiouy]|qu)y$/i'            => '$1ies',
        '/(hive)$/i'                     => '$1s',
        '/(?:([^f])fe|([lr])f)$/i'       => '$1$2ves',
        '/(shea|lea|loa|thie)f$/i'       => '$1ves',
        '/sis$/i'                        => 'ses',
        '/([ti])um$/i'                   => '$1a',
        '/(tomat|potat|ech|her|vet)o$/i' => '$1oes',
        '/(bu)s$/i'                      => '$1ses',
        '/(alias)$/i'                    => '$1es',
        '/(octop)us$/i'                  => '$1i',
        '/(ax|test)is$/i'                => '$1es',
        '/(us)$/i'                       => '$1es',
        '/s$/i'                          => 's',
        '/$/'                            => 's',
    );

    /**
     * @var array
     */
    private static $singular = array(
        '/(quiz)zes$/i'                 => '$1',
        '/(matr)ices$/i'                => '$1ix',
        '/(vert|ind)ices$/i'            => '$1ex',
        '/^(ox)en$/i'                   => '$1',
        '/(alias)es$/i'                 => '$1',
        '/(octop|vir)i$/i'              => '$1us',
        '/(cris|ax|test)es$/i'          => '$1is',
        '/(shoe)s$/i'                   => '$1',
        '/(o)es$/i'                     => '$1',
        '/(bus)es$/i'                   => '$1',
        '/([m|l])ice$/i'                => '$1ouse',
        '/(x|ch|ss|sh)es$/i'            => '$1',
        '/(m)ovies$/i'                  => '$1ovie',
        '/(s)eries$/i'                  => '$1eries',
        '/([^aeiouy]|qu)ies$/i'         => '$1y',
        '/([lr])ves$/i'                 => '$1f',
        '/(tive)s$/i'                   => '$1',
        '/(hive)s$/i'                   => '$1',
        '/(li|wi|kni)ves$/i'            => '$1fe',
        '/(shea|loa|lea|thie)ves$/i'    => '$1f',
        '/(^analy)ses$/i'               => '$1sis',
        '/([ti])a$/i'                   => '$1um',
        '/(n)ews$/i'                    => '$1ews',
        '/(h|bl)ouses$/i'               => '$1ouse',
        '/(corpse)s$/i'                 => '$1',
        '/(us)es$/i'                    => '$1',
        '/(us|ss)$/i'                   => '$1',
        '/s$/i'                         => '',

        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'
                                        => '$1$2sis',
    );

    /**
     * @var array
     */
    private static $irregular = array(
        'move'                          => 'moves',
        'foot'                          => 'feet',
        'goose'                         => 'geese',
        'sex'                           => 'sexes',
        'child'                         => 'children',
        'man'                           => 'men',
        'tooth'                         => 'teeth',
        'person'                        => 'people'
    );

    /**
     * @var array
     */
    private static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment',
    );

    /**
     * Pluralize the given string.
     *
     * @param string $string The string to pluralize
     * @return string The pluralized form of the given `$string`
     */
    public static function pluralize($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable)) {
            return $str;
        }

        // check for irregular singular forms
        foreach (self::$irregular as $pattern => $result) {
            $pattern = "/{$pattern}\$/i";

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // @codeCoverageIgnoreStart
        // will **never** reach this…
        return $string;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Singularize the given string.
     *
     * @param string $string The string to singularize
     * @return string The singularized form of the given `$string`
     */
    public static function singularize($string)
    {
        if (in_array(strtolower($string), self::$uncountable)) {
            return $string;
        }

        foreach (self::$irregular as $result => $pattern) {
            $pattern = "/{$pattern}\$/i";

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // check for matches using regular expressions
        foreach (self::$singular as $pattern => $result) {
            if (preg_match( $pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        return $string;
    }

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
     * @param boolean $ucfirst wheter to uppercase the first character
     * @return string
     */
    public static function camelize($str, $ucfirst = false)
    {
        $replace = str_replace(
            ' ',
            '',
            ucwords(str_replace(array('_', '-'), ' ', strtolower($str)))
        );

        if (!$ucfirst) {
            return lcfirst($replace);
        } else {
            return $replace;
        }
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
            array(
                "\r" => '\\r',
                "\n" => '\\n',
                "\'" => '\\\'',
                "\t" => '\\t',
                "/"  => '\\/',
                "\"" => '\\\\\'',
                "\\" => '\\\\\\'
            )
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
                array(
                    'Á','á','à','À','â','Â','ä','Ä','ã','Ã','å','Å','ð',
                    'é','É','È','è','Ê','ê','Ë','ë','í','Í','ì','Ì','î',
                    'Î','ï','Ï','ñ','Ñ','ó','Ó','Ò','ò','Ô','ô','Ö','ö',
                    'õ','Õ','Ú','ú','ù','Ù','û','Û','ü','Ü','ý','Ý','ÿ',
                    'Ç','ç'
                ),

                array(
                    'A','a','a','A','a','A','a','A','a','A','a','A','o',
                    'e','E','E','e','E','e','E','e','i','I','i','I','i',
                    'I','i','I','n','N','o','O','O','o','O','o','O','o',
                    'o','O','U','u','u','U','u','U','u','U','y','Y','y',
                    'C','c'
                ),

                $str);
    }

    /**
     * Returns a random string with the given length and given string of
     * allowed characters.
     *
     * @param integer $length The length of the random string
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
        return md5($raw);
    }

    /**
     * Verifies if the given (raw) password matches a hash.
     *
     * @param string $raw The password in raw format
     * @param string $hash A hash created by ZfRest_Util_String::password()
     * @return boolean `true` if the password and hash match, or `false`
     *  otherwise
     */
    public static function verifyPassword($raw, $hash)
    {
        return $hash === md5($raw);
    }
}
