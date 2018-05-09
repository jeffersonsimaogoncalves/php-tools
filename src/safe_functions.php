<?php
/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
if (!function_exists('safe_copy')) {
    /**
     * Safe alias for `copy()` function
     * @param string $source Path to the source file
     * @param string $dest The destination path. If dest is a URL, the copy
     *  operation may fail if the wrapper does not support overwriting of
     *  existing files
     * @return bool
     * @since 1.0.5
     */
    function safe_copy($source, $dest)
    {
        //@codingStandardsIgnoreLine
        return @copy($source, $dest);
    }
}

if (!function_exists('safe_mkdir')) {
    /**
     * Safe alias for `mkdir()` function
     * @param string $pathname Path to the directory
     * @param int $mode The mode is 0777 by default, which means the widest
     *  possible access
     * @param bool $recursive Allows the creation of nested directories
     *  specified in the pathname.
     * @return bool
     */
    function safe_mkdir($pathname, $mode = 0777, $recursive = false)
    {
        //@codingStandardsIgnoreLine
        return @mkdir($pathname, $mode, $recursive);
    }
}

if (!function_exists('safe_rmdir')) {
    /**
     * Safe alias for `rmdir()` function
     * @param string $dirname Path to the directory
     * @return bool
     */
    function safe_rmdir($dirname)
    {
        //@codingStandardsIgnoreLine
        return @rmdir($dirname);
    }
}

if (!function_exists('safe_rmdir_recursive')) {
    /**
     * Safe alias for `rmdir_recursive()` function
     * @param string $dirname Path to the directory
     * @return void
     * @since 1.0.6
     */
    function safe_rmdir_recursive($dirname)
    {
        //@codingStandardsIgnoreLine
        @rmdir_recursive($dirname);
    }
}

if (!function_exists('safe_symlink')) {
    /**
     * Safe alias for `symlink()` function
     * @param string $target Target of the link
     * @param string $link The link name
     * @return bool
     */
    function safe_symlink($target, $link)
    {
        //@codingStandardsIgnoreLine
        return @symlink($target, $link);
    }
}

if (!function_exists('safe_unlink')) {
    /**
     * Safe alias for `unlink()` function
     * @param string $filename Path to the file
     * @return bool
     */
    function safe_unlink($filename)
    {
        //@codingStandardsIgnoreLine
        return @unlink($filename);
    }
}

if (!function_exists('safe_unserialize')) {
    /**
     * Safe alias for `unserialize()` function
     * @param string $str The serialized string. If the variable being
     *  unserialized is an object, after successfully reconstructing the object
     *  PHP will automatically attempt to call the __wakeup() member function
     *  (if it exists)
     * @return bool
     * @since 1.0.5
     */
    function safe_unserialize($str)
    {
        //@codingStandardsIgnoreLine
        return @unserialize($str);
    }
}
