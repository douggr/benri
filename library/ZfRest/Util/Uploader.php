<?php
/*
 * douggr/zf-rest
 *
 * @link https://github.com/douggr/zf-rest for the canonical source repository
 * @version 1.1.3
 *
 * For the full copyright and license information, please view the LICENSE
 * file distributed with this source code.
 */

/**
 * Representation of date and time. 
 */
class ZfRest_Util_Uploader
{
    // relative to application base path
    const SAVE_PATH = '/uploads';

    /**
     * Path to uploads
     * @var string
     */
    private $savePath = self::SAVE_PATH;

    /**
     * Save all files at once
     */
    public static function saveAll()
    {
        $uploader = new static();
        $files    = [];

        foreach ($_FILES as $file) {
            $files[] = $uploader->saveFile($file);
        }

        return $files;
    }

    /**
     * 
     */
    public static function save($index)
    {
        if (!array_key_exists($index, $_FILES)) {
            return false;
        }

        return (new static())->saveFile($_FILES[$index]);
    }

    /**
     * 
     */
    public function setSavePath($path)
    {
        $this->savePath = $path;
    }

    /**
     * 
     */
    public function saveFile(array $fileData)
    {
        $path = realpath('/' . trim($this->savePath, '/'));

        if (!$path) {
            return false;
        }

        $name = "{$path}/{$fileData['name']}";

        if (!move_uploaded_file($fileData['tmp_name'], $name)) {
            return false;
        }

        return [
            'name'      => $fileData['name'],
            'path'      => $name,
            'size'      => $fileData['size'],
            'type'      => $fileData['type'],
            'hash'      => md5_file($name),
        ];
    }
}
