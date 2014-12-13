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
 * Helpful string class
 */
class ZfRest_Util_String
{
    /**
     *
     */
    private static $plural = [
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
    ];

    /**
     *
     */
    private static $singular = [
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
    ];

    private static $irregular = [
        'move'                          => 'moves',
        'foot'                          => 'feet',
        'goose'                         => 'geese',
        'sex'                           => 'sexes',
        'child'                         => 'children',
        'man'                           => 'men',
        'tooth'                         => 'teeth',
        'person'                        => 'people'
    ];

    /**
     *
     */
    private static $uncountable = [
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment',
    ];

    /**
     * Plurazize the given string
     *
     * @param string
     * @return string
     */
    public static function pluralize($str)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($str), self::$uncountable)) {
            return $str;
        }

        // check for irregular singular forms
        foreach (self::$irregular as $pattern => $result) {
            $pattern = "/{$pattern}\$/i";

            if (preg_match($pattern, $str)) {
                return preg_replace($pattern, $result, $str);
            }
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $str)) {
                return preg_replace($pattern, $result, $str);
            }
        }

        // @codeCoverageIgnoreStart
        // will **never** reach this…
        return $str;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Singularize the given string
     *
     * @param string
     * @return string
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
     * @param string
     * @return string
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
     * 
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
     * @param string
     * @param boolean wheter to uppercase the first character
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
        } else {
            return $replace;
        }
    }

    /**
     * Quotes the string so that it can be used as Javascript string constants
     * for example.
     *
     * @param string
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
                "/"  => '\\/',
                "\"" => '\\\\\'',
                "\\" => '\\\\\\'
            ]
        );
    }

    /**
     * Replace all ponctuation from the given string.
     *
     * @param string
     * @param string Replacement to apply
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
     * Translate characters to match \w regex
     *
     * @param string
     * @return string
     */
    public static function accentremove($str)
    {
        return str_replace(['Á','á','à','À','â','Â','ä','Ä','ã','Ã','å','Å','ð'
                           ,'é','É','È','è','Ê','ê','Ë','ë','í','Í','ì','Ì','î'
                           ,'Î','ï','Ï','ñ','Ñ','ó','Ó','Ò','ò','Ô','ô','Ö','ö'
                           ,'õ','Õ','Ú','ú','ù','Ù','û','Û','ü','Ü','ý','Ý','ÿ'
                           ,'Ç','ç'],
 
                           ['A','a','a','A','a','A','a','A','a','A','a','A','o'
                           ,'e','E','E','e','E','e','E','e','i','I','i','I','i'
                           ,'I','i','I','n','N','o','O','O','o','O','o','O','o'
                           ,'o','O','U','u','u','U','u','U','u','U','y','Y','y'
                           ,'C','c'],

                           $str);
    }

    /**
     * Returns a random string with the given length and given string of
     * allowed characters.
     *
     * @param integer string length
     * @param string allowed chars
     * @return string
     */
    public static function random(
        $length         = 8,
        $allowedChars   = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXY346789'
    ) {
        $return      = '';
        $hash        = password_hash($allowedChars, PASSWORD_BCRYPT, ['cost' => 4]);
        $hash        = preg_replace("/[^{$allowedChars}]+/", '', $hash);
        $hash_length = strlen($hash) - 1;

        for (;$length > 0; --$length) {
            $return .= $hash{rand(0, $hash_length)};
        }

        return str_shuffle($return);
    }

    /**
     * Creates a password hash.
     *
     * @param string The password in raw format
     * @return string Returns the hashed password, or NULL on failure. 
     */
    public static function password($raw)
    {
        return password_hash($raw, PASSWORD_BCRYPT) ?: null;
    }

    /**
     * Verifies that a password matches a hash.
     *
     * @param string The password in raw format.
     * @param string A hash created by ZfRest_Util_String::password().
     * @return boolean TRUE if the password and hash match, or FALSE otherwise. 
     */
    public static function verifyPassword($raw, $hash)
    {
        return password_verify($raw, $hash);
    }
}
