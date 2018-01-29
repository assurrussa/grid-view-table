<?php

declare(strict_types=1);


namespace Assurrussa\GridView\Helpers;

/**
 * Class F
 * Job with files
 * Работа с файлами
 *
 * @package Assurrussa\AmiCMS\Components\Helpers
 */
class F
{

    /**
     * Shared environment safe version of mkdir. Supports recursive creation.
     * For avoidance of umask side-effects chmod is used.
     *
     * @param string  $dst       path to be created
     * @param integer $mode      the permission to be set for newly created directories, if not set - 0777 will be used
     * @param boolean $recursive whether to create directory structure recursive if parent dirs do not exist
     *
     * @return boolean result of mkdir
     * @see mkdir
     */
    public static function createDirectory(string $dst, int $mode = null, bool $recursive = false): bool
    {
        if ($mode === null) {
            $mode = 0777;
        }
        $prevDir = dirname($dst);
        if ($recursive && !is_dir($dst) && !is_dir($prevDir)) {
            self::createDirectory(dirname($dst), $mode, true);
        }
        $res = true;
        if (!is_dir($dst)) {
            $res = mkdir($dst, $mode);
        }
        @chmod($dst, $mode);

        return $res;
    }
}